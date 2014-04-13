<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.oxwall.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Oxwall software.
 * The Initial Developer of the Original Code is Oxwall Foundation (http://www.oxwall.org/foundation).
 * All portions of the code written by Oxwall Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Oxwall Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Oxwall community software
 * Attribution URL: http://www.oxwall.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package ow.ow_system_plugins.base.comments
 * @since 1.0
 */
class BASE_MCMP_Comments extends BASE_CMP_Comments
{

    /**
     * Constructor.
     *
     * @param BASE_CommentsParams $params
     */
    public function __construct( BASE_CommentsParams $params )
    {
        parent::__construct($params);
    }

    public function initForm()
    {
        OW::getDocument()->addOnloadScript(
            "window.owCommentCmps['$this->id'] = new OwMobileComments('$this->cmpContextId', '$this->formName', '$this->id');"
        );

        $this->setTemplate(OW::getPluginManager()->getPlugin('base')->getMobileCmpViewDir() . 'comments.html');
        $this->params->setCommentCountOnPage(BOL_CommentService::getInstance()->getConfigValue(BOL_CommentService::CONFIG_MB_COMMENTS_ON_PAGE));

        if ( $this->isAuthorized )
        {
            $this->addComponent('form', new BASE_MCMP_CommentsForm($this->params, $this->id, $this->formName));
            $this->assign('formCmp', true);
        }

        $this->addComponent('commentList', new BASE_MCMP_CommentsList($this->params, $this->id));
    }
}