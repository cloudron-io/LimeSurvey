<?php
/*
* LimeSurvey
* Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


/**
 * Description of HttpRequest
 *
 *
 * Used in LSYii_Application.php
 * <pre>
 *    'request'=>array(
 *        'class'=>'HttpRequest',
 *        'noCsrfValidationRoutes'=>array(
 *            '^services/wsdl.*$'
 *        ),
 *        'enableCsrfValidation'=>true,
 *        'enableCookieValidation'=>true,
 *    ),
 * </pre>
 *
 * Every route will be interpreted as a regex pattern.
 *
 */
class LSHttpRequest extends CHttpRequest
{

    private $_pathInfo;
    
    public $noCsrfValidationRoutes = array();

    /**
     * Return the referal url,
     * it's used for the "close" buttons, and the "save and close" buttons
     * So it checks if the referrer url is the same than the current url to avoid looping.
     * If it the case, a paramater can be set to tell what referrer to return.
     * If the referrer is an external url, Yii return by default the current url.
     *
     * DEPRECATED
     * #To avoid looping between two urls (like simpleStatistics <=> Expert Statistics),
     * #it can be necessary to check if the referrer contains a specific word (an action in general)
     * #So if you want to forbid a return to a certain page, just provide an alternative url, and the forbidden key world
     *
     * The checkLoopInNavigationStack-Method will check for looping, though the forbiddenUrl array is not required anymore
     *
     * Not all "close" and "save and close" buttons should use it.
     * Only close button for pages that can be accessed since different places.
     * eg: edit question, that can be accessed from question list or question
     *
     *
     * TODO: implement it for all those pages
     * List of pages where it should be implemented :
     * - All pages accessible via the top nav-bar (eg: create a new survey, edit template, etc.)
     * - All pages accessible via quick actions (home page, survey quick actions, question group quick actions, etc.)
     * - All pages accessible via question explorer (only "add question to group" for now)
     * - Edition of question and question group, which are accessible via summary or list
     * - Import question, question group
     *
     * TODO: remove misused of it
     * It should not be used for pages accessible from only one place
     * - Token activation
     * etc.
     *
     * In doubt, just use getUrlReferrer with a default link to home page for full page layout pages,
     * or a link to the survey summary for sidemenu layout pages,
     * with the controller action as forbidden world.
     * So the close button will never loop.
     *
     * TODO : Each time a new quick action or button is added to access an existing page, the "close" & "save and close" button should be updated to use getUrlReferrer()
     *
     * @param $sAlternativeUrl string, the url to return if referrer url is the same than current url.
     * @return string if success, else null
     */
    public function getUrlReferrer($sAlternativeUrl = null)
    {

        $referrer = parent::getUrlReferrer();
        $baseReferrer    = str_replace(Yii::app()->getBaseUrl(true), "", $referrer);
        $baseRequestUri  = str_replace(Yii::app()->getBaseUrl(), "", Yii::app()->request->requestUri);
        $referrer = ($baseReferrer != $baseRequestUri) ? $referrer : null;
        //Use alternative url if the $referrer is still available in the checkLoopInNavigationStack
        if (($this->checkLoopInNavigationStack($referrer)) || (is_null($referrer))) {
            // Checks if the alternative url should be used
            if (isset($sAlternativeUrl)) {
                $referrer = $sAlternativeUrl;
            } else {
                return App()->createUrl('admin/index');
            }
        }
        return $referrer;
    }

    public function getOriginalUrlReferrer()
    {
        return parent::getUrlReferrer();
    }

    /**
     * Method to update the LimeSurvey Navigation Stack to prevent looping
     */
    public function updateNavigationStack()
    {
        $referrer = parent::getUrlReferrer();
        $navStack = App()->session['LSNAVSTACK'];

        if (!is_array($navStack))
        {
            $navStack = array();
        }

        array_unshift($navStack, $referrer);

        if (count($navStack) > 5)
        {
            array_pop($navStack);
        }
        App()->session['LSNAVSTACK'] = $navStack;
    }

    /**
     * Method to check if an url is part of the stack
     * @return bool Returns true, when an url is saved in the stack
     * @param string $referrerURL The URL that is checked against the stack
     */
    protected function checkLoopInNavigationStack($referrerURL)
    {
        $navStack = App()->session['LSNAVSTACK'];
        foreach ($navStack as $url) {
            $refEqualsUrl = ($referrerURL == $url);
                if ($refEqualsUrl) {
                    return true;
                }
        }
        return false;  
    }

    protected function normalizeRequest() {
        parent::normalizeRequest();

        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $route = Yii::app()->getUrlManager()->parseUrl($this);
        if ($this->enableCsrfValidation) {
            foreach ($this->noCsrfValidationRoutes as $cr) {
                if (preg_match('#'.$cr.'#', $route)) {
                    Yii::app()->detachEventHandler('onBeginRequest',
                        array($this, 'validateCsrfToken'));
                    Yii::trace('Route "'.$route.' passed without CSRF validation');
                    break; // found first route and break
                }
            }
        }
    }


    public function getPathInfo()
    {
        if ($this->_pathInfo === null)
        {
            $pathInfo = $this->getRequestUri();

            if (($pos = strpos($pathInfo, '?')) !== false) {
                            $pathInfo = substr($pathInfo, 0, $pos);
            }

            $pathInfo = $this->decodePathInfo($pathInfo);

            $scriptUrl = $this->getScriptUrl();
            $baseUrl = $this->getBaseUrl();
            if (strpos($pathInfo, $scriptUrl) === 0) {
                            $pathInfo = substr($pathInfo, strlen($scriptUrl));
            } elseif ($baseUrl === '' || strpos($pathInfo, $baseUrl) === 0) {
                            $pathInfo = substr($pathInfo, strlen($baseUrl));
            } elseif (strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0) {
                            $pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
            } else {
                            throw new CException(Yii::t('yii', 'CHttpRequest is unable to determine the path info of the request.'));
            }

            if ($pathInfo === '/') {
                            $pathInfo = '';
            } elseif (!empty($pathInfo) && $pathInfo[0] === '/') {
                            $pathInfo = substr($pathInfo, 1);
            }

            if (($posEnd = strlen($pathInfo) - 1) > 0 && $pathInfo[$posEnd] === '/') {
                            $pathInfo = substr($pathInfo, 0, $posEnd);
            }

            $this->_pathInfo = $pathInfo;
        }
        return $this->_pathInfo;
    }

}
