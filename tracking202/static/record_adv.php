<? include_once($_SERVER['DOCUMENT_ROOT'] . '/202-config/connect.php');

$landing_page_id_public = (int)$_GET['lpip'];
$_values['landing_page_id_public'] = $landing_page_id_public;

$landing_page_id_public = $_values['landing_page_id_public'];
$tracker_row = LandingPages_DAO::find_one_with_user_and_aff_c_id_by_id_public($landing_page_id_public);



//set the timezone to the users timezone
$user_id = $tracker_row['user_id'];
$user_row = Users_DAO::get5($user_id);



//now this sets it
AUTH::set_timezone($user_row['user_timezone']);

if (!$tracker_row) {
	die();
}

if ($_GET['t202id']) {
	//grab tracker data if avaliable
	$_values['tracker_id_public'] = (int)$_GET['t202id'];


	$tracker_id_public = $_values['tracker_id_public'];
	$tracker_row2 = Trackers_DAO::find_one_by_id_public2($tracker_id_public);


	if ($tracker_row2) {
		$tracker_row = array_merge($tracker_row, $tracker_row2);
	}
}
DU::dump($tracker_row);


//INSERT THIS CLICK BELOW, if this click doesn't already exisit

//get mysql variables
$aff_campaign_id = $tracker_row['aff_campaign_id'];
$_values['user_id'] = $tracker_row['user_id'];
$_values['aff_campaign_id'] = $tracker_row['aff_campaign_id'];
$_values['ppc_account_id'] = $tracker_row['ppc_account_id'];
$_values['click_cpc'] = $tracker_row['click_cpc'];
//todo fixed no payout, otherwise click payout === 0?
//$tracker_row['aff_campaign_payout'];
$aff_campaign_id = $_values['aff_campaign_id'];
$_values['click_payout'] = AffCampaigns_DAO::get_payout_by_id($aff_campaign_id);
$_values['click_time'] = time();
$_values['landing_page_id'] = $tracker_row['landing_page_id'];
$_values['text_ad_id'] = $tracker_row['text_ad_id'];

/* ok, if $_GET['OVRAW'] that is a yahoo keyword, if on the REFER, there is a $_GET['q], that is a GOOGLE keyword... */
//so this is going to check the REFERER URL, for a ?q=, which is the ACUTAL KEYWORD searched.
$referer_url_parsed = @parse_url($_GET['referer']);
$referer_url_query = $referer_url_parsed['query'];

@parse_str($referer_url_query, $referer_query);

switch ($user_row['user_keyword_searched_or_bidded']) {

	case "bidded":
		#try to get the bidded keyword first
		if ($_GET['OVKEY']) { //if this is a Y! keyword
			$keyword = (string)$_GET['OVKEY'];
		} elseif ($_GET['t202kw']) {
			$keyword = (string)$_GET['t202kw'];
		} elseif ($referer_query['p']) {
			$keyword = $referer_query['p'];
		} elseif ($_GET['target_passthrough']) { //if this is a mediatraffic! keyword
			$keyword = (string)$_GET['target_passthrough'];
		} else { //if this is a zango, or more keyword
			$keyword = (string)$_GET['keyword'];
		}
		break;
	case "searched":
		#try to get the searched keyword
		if ($referer_query['q']) {
			$keyword = $referer_query['q'];
		} elseif ($referer_query['p']) {
			$keyword = $referer_query['p'];
		} elseif ($_GET['OVRAW']) { //if this is a Y! keyword
			$keyword = (string)$_GET['OVRAW'];
		} elseif ($_GET['target_passthrough']) { //if this is a mediatraffic! keyword
			$keyword = (string)$_GET['target_passthrough'];
		} elseif ($_GET['keyword']) { //if this is a zango, or more keyword
			$keyword = (string)$_GET['keyword'];
		} else {
			$keyword = (string)$_GET['t202kw'];
		}
		break;
}
$keyword = str_replace('%20', ' ', $keyword);
$keyword_id = INDEXES::get_keyword_id($keyword);
$_values['keyword_id'] = $keyword_id;

$c1 = (string)$_GET['c1'];
$c1 = str_replace('%20', ' ', $c1);
$c1_id = INDEXES::get_c1_id($c1);
$_values['c1_id'] = $c1_id;

$c2 = (string)$_GET['c2'];
$c2 = str_replace('%20', ' ', $c2);
$c2_id = INDEXES::get_c2_id($c2);
$_values['c2_id'] = $c2_id;

$c3 = (string)$_GET['c3'];
$c3 = str_replace('%20', ' ', $c3);
$c3_id = INDEXES::get_c3_id($c3);
$_values['c3_id'] = $c3_id;

$c4 = (string)$_GET['c4'];
$c4 = str_replace('%20', ' ', $c4);
$c4_id = INDEXES::get_c4_id($c4);
$_values['c4_id'] = $c4_id;

$ip_id = INDEXES::get_ip_id($_SERVER['HTTP_X_FORWARDED_FOR']);
$_values['ip_id'] = $ip_id;

$id = INDEXES::get_platform_and_browser_id();
$_values['platform_id'] = $id['platform'];
$_values['browser_id'] = $id['browser'];

$_values['click_in'] = 1;
$_values['click_out'] = 0;


//now lets get variables for clicks site
//so this is going to check the REFERER URL, for a ?url=, which is the ACUTAL URL, instead of the google content, pagead2.google....
if ($referer_query['url']) {
	$click_referer_site_url_id = INDEXES::get_site_url_id($referer_query['url']);
} else {
	$click_referer_site_url_id = INDEXES::get_site_url_id($_GET['referer']);
}
$_values['click_referer_site_url_id'] = $click_referer_site_url_id;


//see if this click should be filtered
$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
$user_id = $tracker_row['user_id'];

$click_filtered = FILTER::startFilter($click_id, $ip_id, $ip_address, $user_id);
$_values['click_filtered'] = $click_filtered;


//ok we have the main data, now insert this row
$click_id = ClicksCounter_DAO::getNextId();



//now gather the info for the advance click insert
$_values['click_id'] = $click_id;
$click_id_public = rand(1, 9) . $click_id . rand(1, 9);
$_values['click_id_public'] = (int)$click_id_public;

//because this is a simple landing page, set click_alp (which stands for click advanced landing page, equal to 0)
$_values['click_alp'] = 1;

//ok we have the main data, now insert this row
$click_result = Clicks_DAO::create_for_adv_by($_values);

//ok we have the main data, now insert this row
//$click_result = ClicksSpy_DAO::create_by1($_values);



$click_adv = ClicksAdvance_DAO::create_doc_for_adv_by($_values);

//now we have the click's advance data, now insert this row
$click_adv = ClicksAdvance_DAO::fill_advance_data($click_adv, $_values);



//insert the tracking data
$click_adv = ClicksAdvance_DAO::fill_tracking_data($click_adv, $_values);



if (!$tracker_row['click_cloaking']) {
	$_values['click_cloaking'] = -1;
} else {
	$_values['click_cloaking'] = $tracker_row['click_cloaking'];
}


//ok we have our click recorded table, now lets insert theses
$click_adv = ClicksAdvance_DAO::fill_record_data($click_adv, $_values);



$landing_site_url = $_SERVER['HTTP_REFERER'];
$click_landing_site_url_id = INDEXES::get_site_url_id($landing_site_url);
$_values['click_landing_site_url_id'] = $click_landing_site_url_id;

$old_lp_site_url = 'http://' . $_SERVER['REDIRECT_SERVER_NAME'] . '/lp/' . $landing_page_id_public;

//insert this
$click_adv = ClicksAdvance_DAO::fill_site_data1($click_adv, $_values);


//save it finally
ClicksAdvance_DAO::save($click_adv);

//update the click summary table if this is a 'real click'
#if ($click_filtered == 0) {

$now = time();

$today_day = date('j', time());
$today_month = date('n', time());
$today_year = date('Y', time());

//the click_time is recorded in the middle of the day
$click_time = mktime(12, 0, 0, $today_month, $today_day, $today_year);
$_values['click_time'] = $click_time;

//check to make sure this click_summary doesn't already exist
$check_count = SummaryOverview_DAO::count_by2($_values);


//if this click summary hasn't been recorded do this now
if ($check_count == 0) {
	$insert_result = SummaryOverview_DAO::create_by2($_values);

}
#}

//set the cookie
setClickIdCookie($_values['click_id'], $_values['aff_campaign_id']);

?>


function t202initB() {

var subid ='<?php echo $click_id; ?>';
createCookie('tracking202subid',subid,0);

var pci = '<?php echo $click_id_public; ?>';
createCookie('tracking202pci',pci,0);

}

t202initB();