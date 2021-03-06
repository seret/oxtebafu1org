<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * /init.php
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.mobile
 * @since 1.6.0
 */

OW::getRouter()->addRoute(
    new OW_Route('ocsfaq.faq', 'faq/', 'OCSFAQ_MCTRL_Faq', 'index')
);