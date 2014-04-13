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
final class IVIDEO_BOL_VideoFeaturedService {

    private $videoFeaturedDao;
    private static $classInstance;

    private function __construct() {
        $this->videoFeaturedDao = IVIDEO_BOL_VideoFeaturedDao::getInstance();
    }

    public static function getInstance() {
        if (null === self::$classInstance) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function isFeatured($videoId) {
        return $this->videoFeaturedDao->isFeatured($videoId);
    }

    public function markFeatured($videoId) {
        return $this->videoFeaturedDao->markFeatured($videoId);
    }

    public function markUnfeatured($videoId) {
        return $this->videoFeaturedDao->markUnfeatured($videoId);
    }

}