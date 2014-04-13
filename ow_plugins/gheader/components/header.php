<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package gheader.components
 */
class GHEADER_CMP_Header extends OW_Component
{
    private $groupId;

    /**
     *
     * @var GHEADER_BOL_Service
     */
    private $service;

    /**
     *
     * @var GROUPS_BOL_Service
     */
    private $groupService;

    /**
     *
     * @var GROUPS_BOL_Group
     */
    private $group;

    public function __construct( $groupId )
    {
        parent::__construct();

        $this->groupId = $groupId;

        $urlStatic = OW::getPluginManager()->getPlugin('gheader')->getStaticUrl();
        OW::getDocument()->addScript($urlStatic . 'gheader.js');
        OW::getDocument()->addStyleSheet($urlStatic . 'gheader.css');

        OW::getLanguage()->addKeyForJs('gheader', 'delete_cover_confirmation');
        OW::getLanguage()->addKeyForJs('gheader', 'my_photos_title');

        $this->service = GHEADER_BOL_Service::getInstance();
        $this->groupService = GROUPS_BOL_Service::getInstance();
        $this->group = $this->groupService->findGroupById($this->groupId);
    }

    private function getGroupInfo()
    {
        static $groupInfo = array();

        if ( !empty($groupInfo) )
        {
            return $groupInfo;
        }

        $groupInfo['id'] = $this->group->id;
        $groupInfo['hasImage'] = !empty($this->group->imageHash);
        $groupInfo['image'] = $this->groupService->getGroupImageUrl($this->group);

        $groupInfo['title'] = htmlspecialchars($this->group->title);
        $groupInfo['description'] = $this->group->description;
        $groupInfo['url'] = $this->groupService->getGroupUrl($this->group);
        $groupInfo['time'] = UTIL_DateTime::formatDate($this->group->timeStamp);

        $groupInfo['admin'] = array();
        $groupInfo['admin']['name'] = BOL_UserService::getInstance()->getDisplayName($this->group->userId);
        $groupInfo['admin']['url'] = BOL_UserService::getInstance()->getUserUrl($this->group->userId);

        return $groupInfo;
    }

    private function getConfig()
    {
        $config = array();
        $config['coverHeight'] = $this->service->getConfig($this->groupId, 'coverHeight');
        $config['avatarSize'] = 100;

        return $config;
    }

    /**
     *
     * @return BASE_CMP_ContextAction
     */
    private function getContextToolbar()
    {
        $language = OW::getLanguage();
        $permissions = $this->getPemissions();

        $contextActionMenu = new BASE_CMP_ContextAction();


        $contextParentAction = new BASE_ContextAction();
        $contextParentAction->setKey('gheaderToolbar');
        $contextParentAction->setLabel('<span class="uh-toolbar-add-label">' . $language->text('gheader', 'set_covet_label') . '</span><span class="uh-toolbar-edit-label">' . $language->text('gheader', 'change_covet_label') . '</span>');
        $contextParentAction->setId('uh-toolbar-parent');
        //$contextParentAction->setClass('ow_ic_picture');

        $contextActionMenu->addAction($contextParentAction);

        if ( $permissions['add'] )
        {
            if ( GHEADER_CLASS_PhotoBridge::getInstance()->isActive() )
            {
                $contextAction = new BASE_ContextAction();
                $contextAction->setParentKey($contextParentAction->getKey());
                $contextAction->setLabel($language->text('gheader', 'choose_from_photos_label'));
                $contextAction->setUrl('javascript://');
                $contextAction->setKey('uhChoose');
                $contextAction->setId('uhco-choose');
                $contextAction->setClass('uhco-item uhco-choose');
                $contextAction->setOrder(1);

                $contextActionMenu->addAction($contextAction);
            }

            $contextAction = new BASE_ContextAction();
            $contextAction->setParentKey($contextParentAction->getKey());
            $contextAction->setLabel('<div class="uh-fake-file"><div>' . $language->text('gheader', 'upload_label') . '</div><input type="file" name="file" id="uh-upload-cover" size="1" /></div>');
            $contextAction->setUrl('javascript://');
            $contextAction->setKey('uhUpload');
            $contextAction->setClass('uhco-item uhco-upload');
            $contextAction->setOrder(2);

            $contextActionMenu->addAction($contextAction);
        }

        if ( $permissions['reposition'] )
        {
            $contextAction = new BASE_ContextAction();
            $contextAction->setParentKey($contextParentAction->getKey());
            $contextAction->setLabel($language->text('gheader', 'reposition_label'));
            $contextAction->setUrl('javascript://');
            $contextAction->setKey('uhReposition');
            $contextAction->setId('uhco-reposition');
            $contextAction->setClass('uhco-item uhco-reposition');
            $contextAction->setOrder(3);

            $contextActionMenu->addAction($contextAction);
        }

        if ( $permissions['delete'] )
        {
            $contextAction = new BASE_ContextAction();
            $contextAction->setParentKey($contextParentAction->getKey());
            $contextAction->setLabel($language->text('gheader', 'remove_label'));
            $contextAction->setUrl('javascript://');
            $contextAction->setKey('uhRemove');
            $contextAction->setId('uhco-remove');
            $contextAction->setClass('uhco-item uhco-remove');

            $contextAction->setOrder(4);

            $contextActionMenu->addAction($contextAction);
        }

        return $contextActionMenu;
    }

    public function getToolbar()
    {
        $toolbar = array();

        $groupInfo = $this->getGroupInfo();

        $js = UTIL_JsGenerator::newInstance()
            ->jQueryEvent('#groups_toolbar_flag', 'click', UTIL_JsGenerator::composeJsString('OW.flagContent({$entity}, {$id}, {$title}, {$href}, "groups+flags", {$ownerId});',
                array(
                    'entity' => GROUPS_BOL_Service::WIDGET_PANEL_NAME,
                    'id' => $this->group->id,
                    'title' => $groupInfo['title'],
                    'href' => $groupInfo['url'],
                    'ownerId' => $this->group->userId
                )));

        OW::getDocument()->addOnloadScript($js, 1001);

        if ( $this->groupService->isCurrentUserCanEdit($this->group) )
        {
            $toolbar[] = array(
                'label' => OW::getLanguage()->text('groups', 'edit_btn_label'),
                'href' => OW::getRouter()->urlForRoute('groups-edit', array('groupId' => $this->groupId))
            );
        }

        if ( OW::getUser()->isAuthenticated() && OW::getUser()->getId() != $this->group->userId )
        {
            $toolbar[] = array(
                'label' => OW::getLanguage()->text('base', 'flag'),
                'href' => 'javascript://',
                'id' => 'groups_toolbar_flag'
            );
        }

        $event = new BASE_CLASS_EventCollector('groups.on_toolbar_collect', array('groupId' => $this->groupId));
        OW::getEventManager()->trigger($event);

        foreach ( $event->getData() as $item )
        {
            $toolbar[] = $item;
        }

        return $toolbar;
    }

    public function getPemissions()
    {
        $permissions = array(
            'add' => false,
            'reposition' => false,
            'delete' => false,
            'view' => false
        );

        $selfMode = $this->group->userId == OW::getUser()->getId();
        $moderationMode = OW::getUser()->isAuthorized('gheader');

        if ( $selfMode || $moderationMode )
        {
            $permissions['delete'] = true;
            $permissions['view'] = true;
        }

        if ( $selfMode && OW::getUser()->isAuthorized('gheader', 'add_cover') )
        {
            $permissions['reposition'] = true;
            $permissions['add'] = true;
        }

        if ( !$permissions['view'] && OW::getUser()->isAuthorized('gheader', 'view_cover') )
        {
            $permissions['view'] = true;
        }

        $permissions['controls'] = ($permissions['add']
            || $permissions['reposition']
            || $permissions['delete'])
            && $permissions['view'];

        $permissions['moderation'] = !$selfMode && $moderationMode;

        return $permissions;
    }

    public function getCover()
    {
        $permissions = $this->getPemissions();

        $cover = $permissions['view']
            ? $this->service->findCoverByGroupId($this->groupId)
            : null;

        if ( $cover === null )
        {
            return array(
                'hasCover' => false,
                'src' => null,
                'data' => array(),
                'css' => ''
            );
        }

        $data = $cover->getSettings();

        $css = empty($data['css']) ? array() : $data['css'];

        if ( !empty($data['position']['top']) )
        {
            $css['top'] = $data['position']['top'] . 'px;';
        }

        if ( !empty($data['position']['left']) )
        {
            $css['left'] = $data['position']['left'] . 'px;';
        }

        $cssStr = '';
        foreach ( $css as $k => $v )
        {
            $cssStr .= $k . ': ' . $v  . '; ';
        }

        return array(
            'hasCover' => true,
            'src' => $this->service->getCoverUrl($cover),
            'data' => $data,
            'css' => $cssStr
        );
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $permissions = $this->getPemissions();

        $cover = $this->getCover();
        $this->assign('cover', $cover);

        $permissions['controls'] = !$cover['hasCover'] && $permissions['add']
            || $cover['hasCover'] && $permissions['delete'];

        if ( $permissions['controls'] )
        {
            $contextToolbar = $this->getContextToolbar();
            $this->addComponent('contextToolbar', $contextToolbar);
        }

        $this->assign('group', $this->getGroupInfo());
        $this->assign('config', $this->getConfig());
        $this->assign('toolbar', $this->getToolbar());

        $this->assign('permissions', $permissions);

        $options = array();

        if ( $permissions['view'] )
        {
            $options['userId'] = $this->group->userId;
            $options['groupId'] = $this->groupId;

            $options['cover'] = array(
                'uploader' => OW::getRouter()->urlFor('GHEADER_CTRL_Header', 'uploader'),
                'responder' => OW::getRouter()->urlFor('GHEADER_CTRL_Header', 'rsp'),
                'cover' => $cover,
                'groupId' => $this->groupId,
                'userId' => $this->group->userId,
                'viewOnlyMode' => !$permissions['controls']
            );

            $js = UTIL_JsGenerator::newInstance()->newObject(array('window', 'GHEADER_Header'), 'GHEADER.Header', array($options));

            OW::getDocument()->addOnloadScript($js);
        }
    }
}