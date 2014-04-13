<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class IVIDEO_BOL_VideoCategoryDao extends OW_BaseDao {

    protected function __construct() {
        parent::__construct();
    }

    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function getDtoClassName() {
        return 'IVIDEO_BOL_VideoCategory';
    }

    public function getTableName() {
        return OW_DB_PREFIX . 'ivideo_videos_category';
    }

    public function getAllVideoCategories($page, $limit) {
        $first = ($page - 1 ) * $limit;
        $categoryDao = IVIDEO_BOL_CategoryDao::getInstance();
        $videoDao = IVIDEO_BOL_VideoDao::getInstance();

        $query = "
                    SELECT a.categoryId, b.name, b.description, count( * ) count
                    FROM " . $this->getTableName() . " AS a," . $categoryDao->getTableName() . " AS b," .
                $videoDao->getTableName() . " AS c " .
                "WHERE a.categoryId = b.id
                       AND c.id = a.videoId
                       AND c.status = 'approved'
                    GROUP BY a.categoryId
                    ORDER BY count DESC
                    LIMIT ?, ?
                ";

        return $this->dbo->queryForList($query, array($first, $limit));
    }

    public function setVideoCategories($videoId, $categoryId) {
        $category = new IVIDEO_BOL_VideoCategory();
        $category->videoId = $videoId;
        $category->categoryId = $categoryId;
        IVIDEO_BOL_VideoCategoryDao::getInstance()->save($category);
        return $category->id;
    }

    public function getVideoCategories($videoId) {
        $example = new OW_Example();
        $example->andFieldEqual('videoId', $videoId);

        $categories = $this->findListByExample($example);
        $list = array();

        foreach ($categories as $category) {
            $list[] = IVIDEO_BOL_CategoryService::getInstance()->getCategoryName($category->categoryId);
        }

        if (count($list) > 0)
            return $list;
        else
            return false;
    }

}