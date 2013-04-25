<?php

require_once 'common/Db.php';

/**
 * Dao layer for mongodb collection: sort_referers
 *  - Time: March 18, 2011, 3:39 am
 */

 
class SortReferers_DAO {
	const _coll = 'sort_referers';

	/**
	 * func #139
	 * -used in /tracking202/ajax/sort_referers.php(6249)
	 * -INSERT INTO 202_sort_referers 
			SET user_id = 'vv.user_id', referer_id = 'vv.site_domain_id', sort_referer_clicks = 'vv.sort_referer_clicks', sort_referer_leads = 'vv.sort_referer_leads', sort_referer_su_ratio = 'vv.sort_referer_su_ratio', sort_referer_payout = 'vv.sort_referer_payout', sort_referer_epc = 'vv.sort_referer_epc', sort_referer_avg_cpc = 'vv.sort_referer_avg_cpc', sort_referer_income = 'vv.sort_referer_income', sort_referer_cost = 'vv.sort_referer_cost', sort_referer_net = 'vv.sort_referer_net', sort_referer_roi = 'vv.sort_referer_roi'
	 *
	 * create by values 
	 */
	public static function create_by($_values) {
		//variables passed 
		$user_id = $_values['user_id'];
		//$site_domain_id = $_values['site_domain_id'];
		$referer_id = $_values['referer_id'];
		$sort_referer_clicks = $_values['sort_referer_clicks'];
		$sort_referer_leads = $_values['sort_referer_leads'];
		$sort_referer_su_ratio = $_values['sort_referer_su_ratio'];
		$sort_referer_payout = $_values['sort_referer_payout'];
		$sort_referer_epc = $_values['sort_referer_epc'];
		$sort_referer_avg_cpc = $_values['sort_referer_avg_cpc'];
		$sort_referer_income = $_values['sort_referer_income'];
		$sort_referer_cost = $_values['sort_referer_cost'];
		$sort_referer_net = $_values['sort_referer_net'];
		$sort_referer_roi = $_values['sort_referer_roi'];

		// object to be created
		$data = array('referer_id' => $referer_id, //$site_domain_id,
					'sort_referer_avg_cpc' => $sort_referer_avg_cpc,
					'sort_referer_clicks' => $sort_referer_clicks,
					'sort_referer_cost' => $sort_referer_cost,
					'sort_referer_epc' => $sort_referer_epc,
					'sort_referer_income' => $sort_referer_income,
					'sort_referer_leads' => $sort_referer_leads,
					'sort_referer_net' => $sort_referer_net,
					'sort_referer_payout' => $sort_referer_payout,
					'sort_referer_roi' => $sort_referer_roi,
					'sort_referer_su_ratio' => $sort_referer_su_ratio,
					'user_id' => $user_id);

	  // 处理合理的 null 数据
	  $data = NameUtil::sort_value_null_to_0($data);

		return Db::insert(self::_coll, $data);
	}


	/**
	 * func #137
	 * -used in /tracking202/ajax/sort_referers.php(1518)
	 * -DELETE 
			FROM 202_sort_referers 
			WHERE user_id = 'vv.user_id'
	 *
	 * remove by user id 
	 */
	public static function remove_by_user_id($user_id) {
		

		// query criteria
		$query = array('user_id' => $user_id);
    
		return Db::remove(self::_coll, $query);
	}



}