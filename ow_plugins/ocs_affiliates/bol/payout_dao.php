<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `ocsaffiliates_payout` table.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.bol
 * @since 1.5.3
 */
class OCSAFFILIATES_BOL_PayoutDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var OCSAFFILIATES_BOL_PayoutDao
     */
    private static $classInstance;

    /**
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns an instance of class.
     *
     * @return OCSAFFILIATES_BOL_PayoutDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'OCSAFFILIATES_BOL_Payout';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ocsaffiliates_payout';
    }

    /**
     * @param int $affiliateId
     * @return array
     */
    public function findListByAffiliateId( $affiliateId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('affiliateId', $affiliateId);
        $example->setOrder('`paymentDate` DESC');
        
        return $this->findListByExample($example);
    }

    /**
     * @param int $affiliateId
     */
    public function deleteByAffiliateId( $affiliateId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('affiliateId', $affiliateId);
        
        $this->deleteByExample($example);
    }

    /**
     * Returns affiliate payouts sum
     *
     * @param $affiliateId
     * @return float
     */
    public function getPayoutSum( $affiliateId )
    {
        $sql = 'SELECT SUM(`amount`) FROM `'.$this->getTableName().'`
            WHERE `affiliateId` = :id';

        return $this->dbo->queryForColumn($sql, array('id' => $affiliateId));
    }
}