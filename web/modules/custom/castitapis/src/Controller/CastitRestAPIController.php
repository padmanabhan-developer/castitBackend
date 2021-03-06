<?php

/**
 * @file
 * Contains \Drupal\castitapis\Controller\CastitRestAPIController.
 */

namespace Drupal\castitapis\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\group\Entity\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenCloud\Rackspace;
use Mailgun\Mailgun;

// zencoder old pass : abc-def-ghi-jkl-2
// zencoder new pass : C@$T1T-ZeCoDeR

/**
 * Controller routines for castitapis routes.
 */
class CastitRestAPIController extends ControllerBase {
  private $hair_colors_DK = [
    1 => 'Blondt',
    2 => 'Mørke blondt',
    3 => 'Brunt',
    4 => 'Sort',
    5 => 'Rødt',
    6 => 'Gråt',
    7 => 'Skaldet',
    8 => 'Hvidt'
  ];

  private $hair_colors_EN = [
    1 => 'Blonde',
    2 => 'Dark Blonde',
    3 => 'Brown',
    4 => 'Black',
    5 => 'Red',
    6 => 'Grey',
    7 => 'Bald',
    8 => 'White'
  ];

  private $eye_colors_DK = [
    1 => 'Brune',
    2 => 'Blå',
    3 => 'Grønne',
    4 => 'Blågrønne',
    5 => 'Blågrå',
    6 => 'Grå',
    7 => 'Hazel'
  ];

  private $eye_colors_EN = [
    1 => 'Brown',
    2 => 'Blue',
    3 => 'Green',
    4 => 'Blue Green',
    5 => 'Blue Grey',
    6 => 'Grey',
    7 => 'Hazel'
  ];

  /**
   * Callback for `my-api/get.json` API method.
   */
  public function get_example( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'GET';

    return new JsonResponse( $response );
  }

  /**
   * Callback for `my-api/put.json` API method.
   */
  public function put_example( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'PUT';

    return new JsonResponse( $response );
  }

  /**
   * Callback for `my-api/post.json` API method.
   */
  public function post_example( Request $request ) {

    // This condition checks the `Content-type` and makes sure to
    // decode JSON string from the request body into array.
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
    }

    $response['data'] = 'Some test data to return';
    $response['method'] = 'POST';

    return new JsonResponse( $response );
  }

  public function model( Request $request ) {

    // This condition checks the `Content-type` and makes sure to
    // decode JSON string from the request body into array.
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
    }

    $response['data'] = 'Some test data to return';
    $response['method'] = 'POST';

    return new JsonResponse( $response );
  }

  public function get_payment_info( Request $request ) {

    // This condition checks the `Content-type` and makes sure to
    // decode JSON string from the request body into array.
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );

      if ($data['uid']) {
        $user = User::load($data['uid']);
        $payment_info = $user->field_payments->getValue();
        ppe($payment_info);
      }
    }

    $response['data'] = 'Some test data to return';
    $response['method'] = 'GET';

    return new JsonResponse( $response );
  }

  public function set_payment_info( Request $request ) {

    // This condition checks the `Content-type` and makes sure to
    // decode JSON string from the request body into array.
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      ppe($data);
      $request->request->replace( is_array( $data ) ? $data : [] );
    }

    $response['data'] = 'Some test data to return';
    $response['method'] = 'POST';

    return new JsonResponse( $response );
  }

  public function get_files_access(Request $request){
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $filename = json_decode( $request->query->get('filename'), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );

      $username = 'castit';
      $apikey = '187a515209d0affd473fedaedd6d770b';
      $containerName = 'CASTITFILES';
      $region = 'LON';
      $client = new Rackspace(Rackspace::UK_IDENTITY_ENDPOINT, array(
          'username' => $username,
          'apiKey'   => $apikey,
      ),
      [
        // Guzzle ships with outdated certs
        Rackspace::SSL_CERT_AUTHORITY => 'system',
        Rackspace::CURL_OPTIONS => [
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ],
      ]
      );
      $service = $client->objectStoreService(null, $region);
      $container = $service->getContainer($containerName);

      $object = $container->getObject("");
      $object->setName($filename);

      $account = $service->getAccount();
      $account->setTempUrlSecret();

      $tempUrl = $object->getTemporaryUrl(1800, 'PUT', TRUE);

      $response['tempUrl'] = $tempUrl;
      return new JsonResponse( $response );
    }
    else{
      return new JsonResponse ( ['error' => 'supply json'] );
    }
  }

  public function updateNotes_lightbox( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $uid = (isset($data['user']['uid_export'])) ? $data['user']['uid_export'] : $data['user']['uid'];
      $user = User::load($uid);
      echo "<pre>"; print_r($data['gids'] );exit;
      foreach($data['gids'] as $key => $gid) {
        $comment = $data['comment'];
        $groupValues = ['field_groupnotes' => $comment];
        $group = Group::load($gid);
        $group->removeMember($user);
        $group->addMember($user, $groupValues);
        echo "<pre>"; print_r('alksla');exit;
      }
      $response['message'] = 'success';
    }
    else{
      $response['message'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function addmember_lightbox( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $uid = (isset($data['user']['uid_export'])) ? $data['user']['uid_export'] : $data['user']['uid'];
      $user = User::load($uid);
      // echo "<pre>"; print_r($data);exit;
      foreach($data['gids'] as $key => $gid) {
        $comment = $data['comment'];
        $groupValues = ['field_groupnotes' => $comment];
        $group = Group::load($gid);
        $group->removeMember($user);
        $group->addMember($user, $groupValues);
        // echo "<pre>"; print_r($a);exit;
      }
      $response['message'] = 'success';
    }
    else{
      $response['message'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function update_media_delta( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $user = User::load($data['userData']['uid_export']);
      if($user){
        // PHOTOS ordering
        $existing_photos_data = $user->field_photos->getValue();
        $incoming_photos_data = $data['userData']['field_photos_export'];
        if(count($existing_photos_data) > 0 && count($incoming_photos_data) > 0){
          $new_photos_data = [];
          foreach($existing_photos_data as $existing_key => $existing_obj) {
            $new_key = array_search ($existing_obj['uri'], $incoming_photos_data);
            $new_photos_data[$new_key] = [
              'uri' => $existing_obj['uri'],
              'title' => $existing_obj['title'],
              'options' => $existing_obj['options']
            ];
          }
          ksort($new_photos_data);
          $user->set('field_photos', $new_photos_data);
          $user->save();
        }
        // VIDEOS ordering
        $existing_videos_data = $user->field_videos->getValue();
        $incoming_videos_data = $data['userData']['field_videos_export'];
        if(count($existing_videos_data) > 0 && count($incoming_videos_data) > 0){
          $new_videos_data = [];
          foreach($existing_videos_data as $existing_key => $existing_obj) {
            $new_key = array_search ($existing_obj['uri'], $incoming_videos_data);
            $new_videos_data[$new_key] = [
              'uri' => $existing_obj['uri'],
              'title' => $existing_obj['title'],
              'options' => $existing_obj['options']
            ];
          }
          ksort($new_videos_data);
          $user->set('field_videos', $new_videos_data);
          $user->save();
        }
        // VIDEOS THUMBNAILS ordering
        $existing_videos_data = $user->field_video_thumbnails->getValue();
        $incoming_videos_data = $data['userData']['field_video_thumbnails_export'];
        if(count($existing_videos_data) > 0 && count($incoming_videos_data) > 0){
          $new_videos_data = [];
          foreach($existing_videos_data as $existing_key => $existing_obj) {
            $new_key = array_search ($existing_obj['uri'], $incoming_videos_data);
            $new_videos_data[$new_key] = [
              'uri' => $existing_obj['uri'],
              'title' => $existing_obj['title'],
              'options' => $existing_obj['options']
            ];
          }
          ksort($new_videos_data);
          $user->set('field_video_thumbnails', $new_videos_data);
          $user->save();
        }
      }
      $response['message'] = 'success';
    }
    else{
      $response['message'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function addcomment_lightbox(  ) {

  }

  public function update_password( Request $request ) {
    $response = array();
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $user = user_load_by_mail($data['email']);
      if($user) {
        $user->setPassword($data['pass']);
        $user->save();

      }
      else{
        $response['message'] = 'Profile ERROR';
      }
    }
    else{
      $response['message'] = 'supply json';
    }
    return new JsonResponse( $response );

  }

  public function reset_password( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $user = user_load_by_mail($data['name']);
      if($user) {
        $to = $data['name'];
        $content = '';
        // $to = 'padmanabhann1@mailinator.com';
				$subject = "Castit adgangskode";
        $time = time();
        $user->setPassword($time);
        $first_name = $user->field_first_name->getValue();
        $last_name = $user->field_last_name->getValue();
        $mgClient = new Mailgun('key-ebe8829c00330a3be43c59dd67da5b73');
        $domain = "mail.castit.dk";
        $randompass = base64_encode($time);
        $content .= '<!DOCTYPE html><html lang="en"><head>
        <meta content="text/html; charset=UTF-8" http-equiv="content-type">
        </head>
        <body style="background:#fff; font-family:Calibri;">
        <div style="background:#fff;width:100%;float:left;">
        <div style="width:100%; margin:auto; text-align:center;">
        <div style="display:inline-block; background:#fff; border:solid 3px #313743;
        width:580px;-webkit-border-radius: 8px;-moz-border-radius: 8px;border-radius: 8px;
        padding:0 0 13px; margin:18px 0 50px 0;">
        <div style="color: #20be93;font-size: 23px;font-family: Calibri;
        background:#cccccc;float:left; width:100%; text-align:center; margin:0 0 16px 0;
        padding:8px 0 4px;"><img src="https://castit.dk/images/logo.png"
        width="90px"/> </div>
        <div style="padding:0 30px;">';
        if($user->hasRole('customer')){
          $content_sal_dk = '<h5 style="color: #646e78;font-size: 16px;padding:0;margin: 0; text-align:left;">Kære Kunde,</h5>';
          $content_sal_en = '<h5 style="color: #646e78;font-size: 16px;padding:0;margin: 0; text-align:left;">Dear Customer,</h5>';
        }else{
          $content_sal_dk = '<h5 style="color: #646e78;font-size: 16px;padding:0;margin: 0; text-align:left;">Kære '.ucfirst($first_name[0]['value']).' '.ucfirst($last_name[0]['value']).',</h5>';
        $content_sal_en = '<h5 style="color: #646e78;font-size: 16px;padding:0;margin: 0; text-align:left;">Dear '.ucfirst($first_name[0]['value']).' '.ucfirst($last_name[0]['value']).',</h5>';
        }

        $reset_link_dk = '<a href="https://castit.dk/new-password?email='.$to.'&resethash='.$randompass.'">Nulstil kodeord</a>';
        $reset_link_en = '<a href="https://castit.dk/new-password?email='.$to.'&resethash='.$randompass.'">Reset Password</a>';

        // $reset_link_dk = '<a href="http://ang.castit:4000/new-password?email='.$to.'&resethash='.$randompass.'">Nulstil kodeord</a>';
        // $reset_link_en = '<a href="http://ang.castit:4000/new-password?email='.$to.'&resethash='.$randompass.'">Reset Password</a>';

        $content_sign_dk = '<p style="color: #646e78;text-align:left;font-size: 16px;padding:0 0 45px 0; margin:51px 0 0;
								line-height:20px; text-align:left;font-family:Calibri"><br><br>Med venlig hilsen,<br>Castit</p>
								<div style="float:left; width:100%; margin:40px 0 0 0; border-top:solid 1px #dddddd;
								padding:20px 0 0 0;">';
					$content_sign_en = '<p style="color: #646e78;text-align:left;font-size: 16px;padding:0 0 45px 0; margin:51px 0 0;
								line-height:20px; text-align:left;font-family:Calibri"><br><br>Yours sincerely,<br>Castit</p>
								<div style="float:left; width:100%; margin:40px 0 0 0; border-top:solid 1px #dddddd;
								padding:20px 0 0 0;">';

					$content .= $content_sal_dk.'<p style="color: #646e78;font-size: 16px;padding:0; line-height:18px; text-align:left;
					">Klik på linket og skift din adgangskode.<br/></p><p style="color: #646e78;font-size: 16px;padding:0; line-height:18px; text-align:left;
								">'.$reset_link_dk.'</p>'.$content_sign_dk.'<br>
								'.$content_sal_en.'<p style="color: #646e78;font-size: 16px;padding:0; line-height:18px; text-align:left;" >
								Click on the link and change your password '.$reset_link_en.'
								</p>'.$content_sign_en;

					$content.= '</div></div></div></div></div></body></html>';
        $user->save();
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: Castit <cat@castit.dk>' . "\r\n";
				$headers .= 'Reply-To: <cat@castit.dk>' . "\r\n";
				$headers .= 'Return-Path: <cat@castit.dk>' ."\r\n";
				$headers .= "Organization: CASTIT"."\r\n";
				$headers .= "X-Priority: 3\r\n";
				$headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
				$headers .= 'BCC: padmanabhann@mailinator.com, cat@castit.dk' . "\r\n";

				$result = $mgClient->sendMessage($domain, array(
					'from'    => 'CASTIT <info@castit.dk>',
					'to'      => $to,
					'subject' => $subject,
					'html'    => $content,
					'bcc'	=> 'padmanabhann@mailinator.com',
				));

        $response['message'] = 'success';
        $response['mail'] = $result;
      }
      else{
        $response['message'] = 'ERROR';
      }
    }
    else{
      $response['message'] = 'supply json';
    }
    return new JsonResponse( $response );

  }

  public function removemember_lightbox( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $user = User::load($data['user']['uid']);
        $group = Group::load($data['gids']);
        $group->removeMember($user);
      $response['message'] = 'success';
    }
    else{
      $response['message'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function complete_registration( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
    }

    if( $data['uid'] ) {
      $user = User::load($data['uid']);
      $user->addRole($data['role']);
      $user->activate();
      $user->save();
    }

    $response['data'] = 'Some test data to return';
    $response['method'] = 'POST';
    $response['input'] = $data;
    $response['user'] = $user;
    $response['request'] = $request;
    return new JsonResponse( $response );
  }

  public function delete_lightbox( $gid ) {
    $response['ee'] = $gid;
    return new JsonResponse( $response );
  }

  public function share_lightbox( Request $request) {
    $response = '';
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $postBody = json_decode( $request->getContent(), TRUE );
      $data = $postBody['profiles'];
      // echo '<pre>';
      // var_dump($postBody);exit;
      $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Castit</title>
        <link rel="stylesheet" type="text/css" href="https://castit.dk/assets/css/emailstyle.css" media="all">';

      $html .= "
        </head>";

      $html .= '<body style="margin:0; padding:0; border:0; outline:0; font-size:100%; vertical-align:baseline; font-weight:normal; box-sizing:border-box;line-height:1;background:#fff; font-family:helveticaneueltstdbd; font-size:14px; overflow-x:hidden;">
        <link itemprop="url" rel="stylesheet" type="text/css" href="https://castit.dk/assets/css/emailstyle.css" media="all">
        <div id="popup-wrapper"  style="float:left; width:100%; padding:54px 0 0 0;" >
           <div class="popup-container" style="margin: auto ; width: 730px ; padding: 0 15px ; max-width: 100%" >
                 <div class="popup-row1" style="border-bottom: solid 1px #2d2e32 ; float: left ; width: 730px; padding: 0 0 20px 0; margin: 0px 0 10px -5px;" >
					   <div class="popup-logo" style="float:left; width:182px;" ><a href="javascript:void(0)"><img style="max-width: 150px; margin-bottom: 15px;"  src="https://castit.dk/images/new_logo_black.png" alt="" /></a>
					 	<a href="https://castit.dk/lightbox-info/'. $postBody['groupID'] .'?sharedBy='.$postBody['emailInfo']['to'].'" style="background-color: blue;border-radius: 20px;padding: 7px 17px;color: #FFF;text-decoration: none;font-family: helvetica;font-size: 13px;">Open Lightbox</a>
					   </div>
                       <div class="popup-text" style="display:block; padding:0 0 0 182px;">
                             <h4 style="padding:0;color:#000; font-size:16px; line-height:20px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; margin:0 0 20px 0;" >Castit Lightbox: '.$postBody['groupName'].'</h4>
                            <p style="color:#dddddd; font-size:14px; line-height:20px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; margin:0;">'.$postBody['emailInfo']['comment'].'.</p>
                       </div>
                  </div><!--popup-row1-->
                  <div class="popup-row2" style="clear: both ; margin: 0 0;width: 730px;">';
      foreach($data[0] as $member){
        $profile_image = $member['field_photos_export'][0]['url'];
        $html .= '<div class="pop-col3" style="float:left; padding:0 0; margin:0 0 20px 0;border: 5px solid white; width: 170px !important;">
              <div class="pop-col-inner" style=""float:left; width:100%; position:relative;>
                <div class="pop-thumb" style="float:left; width:100%; position:relative; margin:0 0 10px 0; background-image: url('.$profile_image.'); height:217px;background-size: cover;background-repeat: no-repeat;background-position: top center;">
                <h6 style="font-weight:normal; margin:0; margin-top:100% !important; padding:0;color:#fff; font-size:10px; line-height:20px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; padding:13px 0 13px 0; background:rgba(0,0,0,0.75); position:absolute; left:0; bottom:0; width:100%; text-align:center;" >'.$member['field_first_name_export'].'.&nbsp;'.$member['field_profile_number'].'</h6>
              </div>
              <h5 style="margin:0; padding:0;color:#000; font-size:12px; line-height:16px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; margin:0 0 0 0;" >Note: </h5>
                  <p style="color:#d1d1d1; font-size:12px; line-height:16px; font-weight:normal; font-family:Arial, Helvetica, sans-serif; margin:0 0 0 0;">'.$member['field_groupnotes'].'</p>
                </div>
              </div>';
			}
      $html .= '</div><!--popup-row2-->

        <div class="popup-row3" style="float:left; width:100%; margin:60px 0 0 0 ;border-top:solid 1px #2d2e32; padding:30px 0;">
             <span class="popup-icon1" style="float:left"><img style="max-width:100%;"  src="https://castit.dk/images/group_icon.png" alt="" /></span>
             <h3 style="font-weight:bold; margin:0; padding:0; float:right; font-size:32px; color:#000;font-family: helvetica;" >'.date('d-m-Y').'</h3>
        </div><!--popup-row3-->

   </div>
</div>

</body>
</html>';

  $subject = "Castit Lighbox : ". $postBody['groupName'];

  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= 'From: Castit <cat@castit.dk>' . "\r\n";
  $headers .= 'Reply-To: <'.$postBody['emailInfo']['selfEmail'].'>' . "\r\n";
  $headers .= 'Return-Path: <cat@castit.dk>' ."\r\n";
  $headers .= "Organization: CASTIT"."\r\n";
  $headers .= "X-Priority: 3\r\n";
  $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
  $headers .= 'BCC: padmanabhann@mailinator.com' . "\r\n";


  $email_body = $html;

  $mgClient = new Mailgun('key-ebe8829c00330a3be43c59dd67da5b73');
  $domain = "mail.castit.dk";

  $result = $mgClient->sendMessage($domain, array(
    'from'    => 'CASTIT <info@castit.dk>',
    'to'      => $postBody['emailInfo']['to'],
    'subject' => $subject,
    'html'    => $email_body,
    'bcc'	=> 'padmanabhann@mailinator.com',
    ));
    }

    return new JsonResponse( $result );
  }

  public function create_lightbox( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      // $request->request->replace( is_array( $data ) ? $data : [] );
      $lightbox_name = $data['name'];
      $lightbox_owner = (int) $data['uid'];
      // $lightbox_owner = User::load($data['uid'])->id();
      // $lightbox_owner = \Drupal::currentUser()->id();
      $application_group = Group::create(['type' => 'lightbox', 'uid' => $lightbox_owner]);
      $application_group->set('label', $lightbox_name);
      // $application_group->setOwnerId($lightbox_owner);
      $application_group->save();
      $response['message'] = 'Group Created';
      $response['info'] = [$lightbox_name, $lightbox_owner, $data, $application_group];
    }
    else{
      $response['message'] = 'request type is not json';
    }
    return new JsonResponse( $response );
  }

  public function video_zencode($filename) {

    $zencoder_input   	= "cf+uk://castit:187a515209d0affd473fedaedd6d770b@CASTITFILES/".$filename;
    $zencoder_output  	= "cf+uk://castit:187a515209d0affd473fedaedd6d770b@CASTITFILES/".$filename.".mp4";
    $zencoder_base_url  = "cf+uk://castit:187a515209d0affd473fedaedd6d770b@CASTITFILES";

    $zencoder_array = [
      "input_file"		=> $zencoder_input,
      "output_file"		=> $zencoder_output,
      "base_url"		=> $zencoder_base_url,
      "filename"		=> $filename,
    ];

    // $zencoder_json = json_encode($zencoder_array);
    $zencoder_json = $this->build_json_zencoder($zencoder_array);


    $url = 'https://app.zencoder.com/api/v2/jobs';
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $zencoder_json);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , 1);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json ',
      'Zencoder-Api-Key: 9477541a57e1eb2471b1ff256ca4b92c'
    ));

		$response = curl_exec( $ch );
    return $response;
  }

  public function build_json_zencoder($data_array){
    $json = '{
      "input": "'.$data_array["input_file"].'",
      "outputs": [
        {"thumbnails": [
          {
            "base_url": "'.$data_array["base_url"].'",
            "label": "regular",
            "number": 1,
            "filename": "thumb_'.$data_array["filename"].'",
            "public": "true"
          }]
    },
    {"label": "mp4 high"},
    {"url": "'.$data_array["output_file"].'"},
    {"h264_profile": "high"}
    ]
    }';
    return $json;
  }

  public function trigger_zencoder( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );

      $filename = $data['filename'];
      $response = $this->video_zencode($filename);
    }
    else{
      $response['error'] = 'supply json';
    }

    return new JsonResponse( $response );
  }
  /**
   * Callback for `my-api/delete.json` API method.
   */
  public function delete_example( Request $request ) {

    $response['data'] = 'Some test data to return';
    $response['method'] = 'DELETE';

    return new JsonResponse( $response );
  }

  public function check_email_exists( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $ids = \Drupal::entityQuery('user')
      ->condition('mail', $data['email'])
      ->execute();
      if (!empty($ids)) {
        $response['message'] = 'exists';
      } else {
        $response['message'] = 'no';
      }
      return new JsonResponse( $response );
    }
  }

  public function set_user_status( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      if (isset($data['uid']) && $data['uid'] != '') {
        $user = User::load($data['uid']);
        if($user){
          $user->set('field_profile_status', $data['status']);
          if($data['status'] == 4){
            $user->set('mail', 'deleted-'.time().'-'.$user->getEmail());
          }
          $user->save();
        }
        $response['message'] = 'success';
        $response['data'] = $data;
      }
    }
    else{
      $response['error'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function set_profile_new_status( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      if (isset($data['uid']) && $data['uid'] != '') {
        $user = User::load($data['uid']);
        if($user){
          $user->set('field_new_profile', $data['value']);
          $user->save();
        }
        $response['message'] = 'success';
        $response['profile_new_status'] = $user->field_new_profile->getValue();
      }
    }
    else{
      $response['error'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function set_profile_type( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $response = array();
      if (isset($data['uid']) && $data['uid'] != '') {
        $user = User::load($data['uid']);
        if($user && ($data['value']=='C' || $data['value']=='Y' || $data['value']=='B')){
          $profileNumber = $user->field_profile_number->getValue();
          $new_profileNumber = substr_replace($profileNumber[0]['value'], $data['value'], 0, 1);
          $user->set('field_profile_type', $data['value']);
          $user->set('field_profile_number', $new_profileNumber);
          if($data['value']=='B'){
            $user->set('field_profile_status', 5); // 5 => Bureau
          }
          $user->save();
          $response['message'] = 'success';
          $response['field_profile_type'] = $user->field_profile_type->getValue()[0]['value'];
          $response['field_profile_number'] = $user->field_profile_number->getValue()[0]['value'];
        }

      }
    }
    else{
      $response['error'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function set_media_status( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      if (isset($data['uid']) && $data['uid'] != '') {
        $user = User::load($data['uid']);
        // exit;
        if($user){
          if (isset($data['type']) && isset($data['index']) && isset($data['status'])) {
            switch($data['type']){
              case 'photo':
                $current_media_info = $user->field_photos->getValue();
                $current_media_info[$data['index']]['title'] = $data['status'];
                $user->set("field_photos", $current_media_info);
                $user->save();
                break;
              case 'video':
                $current_media_info = $user->field_videos->getValue();
                // pp($current_media_info);
                $current_media_info[$data['index']]['title'] = $data['status'];
                // ppe($current_media_info);
                $user->set("field_videos", $current_media_info);
                $user->save();
                break;
              default:
                break;
            }
          }
        }
        $response['message'] = 'success';
        $response['info'] = 'media status updated';
      }
    }
    else{
      $response['error'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function update_profileNumber( User $user, $data) {
    $profileNumber = $data['field_profile_number_export'];
    if($profileNumber == '') {
      $profileNumber = 'Y';
      $profileNumber .= ($data['field_gender_export'] == '1') ? 'M' : 'F';
      $profileNumber .= str_pad($user->id(), 4, "0", STR_PAD_LEFT);
    }
    $user->set('field_profile_number', $profileNumber);
    $user->save();
    return $user;
  }

  public function update_model_fields( User $user , $data) {
    if(!$data['field_height_export']){
      $data['field_height_export'] = $data['field_height'];
    }
    if(!$data['field_weight_export']){
      $data['field_weight_export'] = $data['field_weight'];
    }
    if (!is_numeric($data['field_eye_color_export'])) {
      if (in_array($data['field_eye_color_export'], $this->eye_colors_DK)) {
        $data['field_eye_color_export'] = array_search($data['field_eye_color_export'], $this->eye_colors_DK);
      }
      if (in_array($data['field_eye_color_export'], $this->eye_colors_EN)) {
        $data['field_eye_color_export'] = array_search($data['field_eye_color_export'], $this->eye_colors_EN);
      }
    }

    if (!is_numeric($data['field_hair_color_export'])) {
      if (in_array($data['field_hair_color_export'], $this->hair_colors_DK)) {
        $data['field_hair_color_export'] = array_search($data['field_hair_color_export'], $this->hair_colors_DK);
      }
      if (in_array($data['field_hair_color_export'], $this->hair_colors_EN)) {
        $data['field_hair_color_export'] = array_search($data['field_hair_color_export'], $this->hair_colors_EN);
      }
    }

    if (!$data['field_profile_status_export']) {
      $data['field_profile_status_export'] = 3;
    }

    if (!$data['field_profile_type_export']) {
      $data['field_profile_type_export'] = 'Y';
    }


    // $data['field_hair_color_export']

    $user->set('field_about_me', $data['field_about_me_export']);
    $user->set('field_address', $data['field_address_export']);
    $user->set('field_agreed_to_terms', $data['field_agreed_to_terms_export']);
    $user->set('field_birthday', $data['field_birthday_export']);
    $user->set('field_bra_size', $data['field_bra_size_export']);
    $user->set('field_bureau', $data['field_bureau_export']);
    $user->set('field_category', $data['field_category_export']);
    $user->set('field_cellphone', $data['field_cellphone_export']);
    $user->set('field_city', $data['field_city_export']);
    $user->set('field_country', $data['field_country_export']);
    $user->set('field_dialect_one', $data['field_dialect_one_export']);
    $user->set('field_dialect_two', $data['field_dialect_two_export']);
    $user->set('field_dialect_three', $data['field_dialect_three_export']);
    $user->set('field_ethnic_origin', $data['field_ethnic_origin_export']);
    $user->set('field_eye_color', $data['field_eye_color_export']);
    $user->set('field_fax', $data['field_fax_export']);
    $user->set('field_first_name', $data['field_first_name_export']);
    $user->set('field_gender', $data['field_gender_export']);
    $user->set('field_hair_color', $data['field_hair_color_export']);
    $user->set('field_height', $data['field_height_export']);
    $user->set('field_language_four', $data['field_language_four_export']);
    $user->set('field_language_four_rating', $data['field_language_four_rating_export']);
    $user->set('field_language_one', $data['field_language_one_export']);
    $user->set('field_language_one_rating', $data['field_language_one_rating_export']);
    $user->set('field_language_three', $data['field_language_three_export']);
    $user->set('field_language_three_rating', $data['field_language_three_rating_export']);
    $user->set('field_language_two', $data['field_language_two_export']);
    $user->set('field_language_two_rating', $data['field_language_two_rating_export']);
    $user->set('field_last_name', $data['field_last_name_export']);
    $user->set('field_licenses', $data['field_licenses_export']);
    $user->set('field_nationality', $data['field_nationality_export']);
    $user->set('field_new_from', $data['field_new_from_export']);
    $user->set('field_new_profile', $data['field_new_profile_export']);
    $user->set('field_new_until', $data['field_new_until_export']);
    $user->set('field_occupation', $data['field_occupation_export']);
    $user->set('field_old_profile_id', $data['field_old_profile_id_export']);
    $user->set('field_pant_size_from', $data['field_pant_size_from_export']);
    $user->set('field_pant_size_to', $data['field_pant_size_to_export']);
    // $user->set('field_profile_number', $profileNumber);
    $user->set('field_profile_status', $data['field_profile_status_export']);
    $user->set('field_profile_type', $data['field_profile_type_export']);
    $user->set('field_recently_updated', TRUE);
    $user->set('field_shirt_size_from', $data['field_shirt_size_from_export']);
    $user->set('field_shirt_size_to', $data['field_shirt_size_to_export']);
    $user->set('field_shoe_size_from', $data['field_shoe_size_from_export']);
    $user->set('field_shoe_size_to', $data['field_shoe_size_to_export']);
    $user->set('field_skills', $data['field_skills_export']);
    $user->set('field_sports_and_hobby', $data['field_sports_and_hobby_export']);
    $user->set('field_suit_size_from', $data['field_suit_size_from_export']);
    $user->set('field_suit_size_to', $data['field_suit_size_to_export']);
    $user->set('field_telephone', $data['field_telephone_export']);
    $user->set('field_weight', $data['field_weight_export']);
    $user->set('field_zipcode', $data['field_zipcode_export']);
    $user->set('field_photos', $data['field_photos_export']);
    $user->set('field_photo_thumbnails', $data['field_photo_thumbnails_export']);
    $user->set('field_videos', $data['field_videos_export']);
    $user->set('field_video_thumbnails', $data['field_video_thumbnails_export']);
    return $user;
  }

  public function update_customer_fields( User $user , $data) {
    $user->set('field_organization', $data['field_organization_export']);
    $user->set('field_telephone', $data['field_telephone_export']);
    // echo new JsonResponse( $user );
    // echo new JsonResponse( $data );
    // return new JsonResponse( $data );
    // exit;
    return $user;
  }

  public function user_update( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
      if($data['uid_export'] != ''){
        $user = User::load($data['uid_export']);
        switch ($data['roleType']) {
          case 'model':
          case '':
          default:
            $user = $this->update_model_fields($user, $data);
            break;
          case 'customer':
            $user = $this->update_customer_fields($user, $data);
        }
        $user->save();
        $this->update_profileNumber($user, $data);
        $response['message'] = 'update success';
        $response['uid'] = $user->id();
        $response['profile_number'] = $user->field_profile_number->getValue();
      }
      else {
        $user = User::create([
          'name'=> $data['name_export'],
          'mail'=> $data['name_export'],
          'pass'=> $data['password']
        ]);
        switch ($data['roleType']) {
          case 'model':
          case '':
          default:
            $user = $this->update_model_fields($user, $data);
            // $user->set('field_profile_status', '3');
            $this->update_profileNumber($user, $data);
            $user->addRole('model');
            break;
          case 'customer':
            $user = $this->update_customer_fields($user, $data);
            // exit;
            $user->addRole('customer');
        }
        $user->activate();
        // if($data['roleType'] == 'model') {
        //   $user->set('field_profile_status', '3');
        // }
        $user->save();
        $userObj = $user;
        if($data['roleType'] == 'model') {
          $this->update_profileNumber($user, $data);
        }

        $response['message'] = 'create success';
        $response['uid'] = $user->id();
        $pn = $user->field_profile_number->getValue();
        if(count($pn) > 0){
          $response['profile_number'] = $pn[0]['value'];
        }

        if($data['roleType'] == 'model') {
          $this->send_welcome_email($userObj);
        }
        if($data['roleType'] == 'customer') {
          $this->send_welcome_email_customer($userObj);
        }

        unset($userObj);
      }
    }
    else{
      $response['error'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function enquire_workshop( Request $request ) {
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $data = $data['data'];
      // $request->request->replace( is_array( $data ) ? $data : [] );
      // print_r($data);exit;
      $to_email = $data['to'];
      $to_email = 'padmanabhanncastit@mailinator.com';
      $from_email = $data['from'];
      $from_name = $data['fromName'];
      $mail_body = $data['comment'];
      $to_cc = $data['cc'];
      $html = '<table style="text-align:left" cellspacing="0" cellpadding="0" width="556" border="0">
        <tbody>
        <tr>
        <td>
        <table style="width:100%" border="0">
        <tbody>
        <tr>
        <td align="left"><img alt="Mailtoplogo" src="https://castit.dk/assets/mailTopLogo.png" ></td>
        <td style="width:270px;padding-top:18px" align="left" valign="top"><b style="color:#696969">Castit <span class="il">Workshop</span>:</b><br>'.$mail_body.'</td>
        </tr>
        <tr>
        <td colspan="2"></td>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
        <tr>';

      $subject = "Castit Workshop enquiry";

      $mgClient = new Mailgun('key-ebe8829c00330a3be43c59dd67da5b73');
      $domain = "mail.castit.dk";

      $email_data = array();
      $email_data['from'] = $from_name. ' via Castit.dk <' . $from_email . '>';
      $email_data['to']		=	$to_email;
      $email_data['subject']		=	$subject;
      $email_data['html']		=	$html;
      if(isset($to_cc) && $to_cc != '') {$email_data['cc']		=	$to_cc;}
      $email_data['bcc']		=	'padmanabhann@mailinator.com, cat@castit.dk';


      $result = $mgClient->sendMessage($domain, $email_data);
      $response['success'] = true;
      $response['message'] = 'Email er sendt!';
    }
    else {
      $response['error'] = 'supply json';
    }
    return new JsonResponse( $response );
  }

  public function send_welcome_email($user){
    $to_email = $user->getEmail();
    $first_name = $user->field_first_name->getValue();
    $first_name = $first_name[0]['value'];
    $from = 'cat@castit.dk';
    $subject  = "Tak for din ansøgning!  /  Thank you for your application!";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Castit <cat@castit.dk>' . "\r\n";
    $headers .= 'Reply-To: <'.$from.'>' . "\r\n";
    $headers .= 'Return-Path: <cat@castit.dk>' ."\r\n";
    $headers .= "Organization: CASTIT"."\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
    $headers .= 'BCC: padmanabhann@mailinator.com' . "\r\n";
    $html_body = <<< EOM
    <p>Kære $first_name,</p>
    <p>
    Tusind tak for din ansøgning. Så snart vi har kigget den igennem, modtager du en mail med information om vi lægger din profil Online eller Offline.
    </p>
    <p>
    Vi bestræber os på at svare inden 14 dage.
    </p>
    <p>
    De bedste hilsner
    </p>
    <p>Cathrine & Pernille</p>
    <br/>
    <img style="max-width: 150px;" src="https://castit.dk/images/new_logo_black.png" alt="" />
    <p>Rosenvængets Allè 11, 1. Sal</p>
    <p>2100 København Ø</p>


    <p>Cathrine Hovmand</p>
    <p># 0045 2128 5825</p>
    <p>E: cat@castit.dk</p>

    <p>Pernille Marco: </p>
    <p># 0045 3135 3579</p>
    <p>E: pernille@castit.dk</p>

    <a href="https://castit.dk">Castit.dk</a>
    <br/>
    ------------------------------------------------------------------
    <br/>

    <p>Dear $first_name,</p>
    <p>
    Thank you for your application. As soon as we have looked it through, you will receive an email with information about whether we will add your profile Online or Offline.  We strive to respond within 14 days.
    </p>
    <p>
    Very best
    </p>
    <p>Cathrine & Pernille</p>
    <br/>
    <img style="max-width: 150px;" src="https://castit.dk/images/new_logo_black.png" alt="" />
    <p>Rosenvængets Allè 11, 1. Sal</p>
    <p>2100 København Ø</p>


    <p>Cathrine Hovmand</p>
    <p># 0045 2128 5825</p>
    <p>E: cat@castit.dk</p>

    <p>Pernille Marco: </p>
    <p># 0045 3135 3579</p>
    <p>E: pernille@castit.dk</p>

    <a href="https://castit.dk">Castit.dk</a>
EOM;
    $mgClient = new Mailgun('key-ebe8829c00330a3be43c59dd67da5b73');
    $domain = "mail.castit.dk";
    $result = $mgClient->sendMessage(
      $domain,
      [
      'from'    => 'CASTIT <info@castit.dk>',
      'to'      => $to_email,
      'subject' => $subject,
      'html'    => $html_body,
      'bcc'	=> 'padmanabhann@mailinator.com, cat@castit.dk'
      ]);
      // $response['success'] = TRUE;
    $response['message'] = 'Email er sendt!';
    $response['email'] = $to_email;
    return $response;
  }

  public function send_welcome_email_customer($user){
    $to_email = $user->getEmail();
    $subject  = "Tak for din oprettelse!  /  Thank you for signing up!";
    $html_body = <<< EOM
    <p>Kære Kunde,</p>
    <p>
    Tak for din oprettelse. Du kan nu benytte Lightbox på <a href="https://castit.dk">Castit.dk</a>
    </p>
    <p>
    Kontakt os endelig hvis du har spørgsmål eller brug for hjælp.
    </p>
    <p>
    Mange hilsner
    </p>
    <p>Cathrine Hovmand</p>
    <p># 0045 2128 5825</p>
    <p>E: cat@castit.dk</p>
    <br/>
    <img style="max-width: 150px;" src="https://castit.dk/images/new_logo_black.png" alt="" />
    <p>Rosenvængets Allè 11, 1. Sal</p>
    <p>2100 København Ø</p>
    <a href="https://castit.dk">Castit.dk</a>
    <br/>
    ------------------------------------------------------------------
    <br/>

    <p>Dear Customer,</p>
    <p>
    Thank you for signing up. You can now use the Lightbox on <a href="https://castit.dk">Castit.dk</a>
    </p>
    <p>
    Don't hesitate to contact us if you have any questions
    </p>
    <p>
    Very best
    </p>
    <p>Cathrine Hovmand</p>
    <p># 0045 2128 5825</p>
    <p>E: cat@castit.dk</p>
    <br/>
    <img style="max-width: 150px;" src="https://castit.dk/images/new_logo_black.png" alt="" />
    <p>Rosenvængets Allè 11, 1st floor</p>
    <p>2100 Copenhagen Ø</p>
    <a href="https://castit.dk">Castit.dk</a>

EOM;
      $mgClient = new Mailgun('key-ebe8829c00330a3be43c59dd67da5b73');
      $domain = "mail.castit.dk";
      $result = $mgClient->sendMessage(
        $domain,
        [
          'from'    => 'CASTIT <info@castit.dk>',
          'to'      => $to_email,
          'subject' => $subject,
          'html'    => $html_body,
          'bcc'	=> 'padmanabhann@mailinator.com',
        ]);
      $response['message'] = 'Email er sendt!';
      $response['email'] = $to_email;
      return $response;
    }

  public function send_activation_email( Request $request ) {
    $response['message'] = '';
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      // var_dump($data);
      if ($data['uid']) {
        $user = User::load($data['uid']);
        if ($user) {
          $first_name = trim(ucwords($user->field_first_name->getValue()[0]['value']));
          $to_email = $user->getEmail();
          // var_dump($first_name);
          // var_dump($to_email);
          // exit;
          $subject = "Svar fra Castit! / Response from Castit! ";
          $html_body = <<< EOM
          <p>Kære $first_name,</p>

          <p>Mange tak for din ansøgning som profil hos Castit.
          Vi sætter stor pris på din interesse.</p>

          <p>Vi er glade for at kunne fortælle dig at din profil nu er lagt online, og vi håber at du fortsat vil opdatere den med nye billeder og informationer i fremtiden.</p>

          <p>Desværre kan vi ikke garantere dig jobs, men vi vil kontakte dig såfremt vi får noget spændende ind som kunne passe til dig.</p>

          <p>BEMÆRK: Hvis du ligger hos andre bureauer  vil vi meget gerne vide det.</p>

          <p>Hvis du er interesseret så afholder vi workshops for vores profiler i samarbejde med Tony Grahn. Vi har stor succes med at uddanne vores profiler, så de har nemmere ved at få jobs og som en ekstra gevinst får de en masse personligt ud af det også. Kig på vores hjemmeside under ”Workshops” for mere information.</p>

          <p>Vi glæder os til forhåbentligt snart, at tale med dig.</p>

          <p>De bedste hilsner</p>

          <p>Cathrine & Pernille</p>
          <p></p>
          <br/>
          <img style="max-width: 150px;" src="https://castit.dk/images/new_logo_black.png" alt="" />
          <p>Rosenvængets Allè 11, 1. Sal</p>
          <p>2100 København Ø</p>


          <p>Cathrine Hovmand</p>
          <p># 0045 2128 5825</p>
          <p>E: cat@castit.dk</p>

          <p>Pernille Marco: </p>
          <p># 0045 3135 3579</p>
          <p>E: pernille@castit.dk</p>

          <a href="https://castit.dk">Castit.dk</a>
          <br/>
          -------------------------------------------------------------------------------------------------------


          <p>Dear $first_name</p>
          <br/>Thank you for your application as a profile at Castit. We appreciate your interest.</p>
          <p>We are happy to tell you that your profile is now online and we hope that you will continue to update it with new pictures and information in the future.</p>
          <p>Unfortunately, we cannot guarantee you jobs, but we will contact you if we get something interesting that fits you.</p>
          <br/>
          <p>PLEASE NOTE: If you are working with other agencies, we would like to know.</p>
          <br/>
          <p>If you are interested!</p>
          <p>We organize workshops for our profiles together with Tony Grahn. We have great success in educating our profiles. That makes it easier to access jobs and our profiles get a lot of personal gains as well. Look at our website under "Workshops" or contact us directly.</p>
          <br/><br/>

          <p>We are looking forward to hopefully talk to you soon.</p>
          <br/>
          <p>Very best</p>
          <br/>
          <p>Cathrine & Pernille</p>
          <p></p>
          <br/>
          <img style="max-width: 150px;"  src="https://castit.dk/images/new_logo_black.png" alt="" />
          <p>Rosenvængets Allè 11, 1st floor</p>
          <p>2100 Copenhagen East</p>
          <br/>
          <p>Cathrine Hovmand</p>
          <p># 0045 2128 5825</p>
          <p>E: cat@castit.dk</p>
          <br/>
          <p>Pernille Marco:</p>
          <p># 0045 3135 3579</p>
          <p>E: pernille@castit.dk</p>
          <br/>
          <a href="https://castit.dk">Castit.dk</a>
EOM;
          // $to_email = 'padmanabhann1@mailinator.com';
          $mgClient = new Mailgun('key-ebe8829c00330a3be43c59dd67da5b73');
          $domain = "mail.castit.dk";
          $result = $mgClient->sendMessage(
            $domain,
            [
              'from'    => 'CASTIT <info@castit.dk>',
              'to'      => $to_email,
              'subject' => $subject,
              'html'    => $html_body,
              'bcc'	=> 'padmanabhann@mailinator.com',
            ]);
          $response['message'] = 'Email er sendt!';
          $response['email'] = $to_email;
        }
      }
    }
    return new JsonResponse( $response );
  }

  public function send_deactivation_email( Request $request ) {
    $response['message'] = '';
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      // var_dump($data);
      if ($data['uid']) {
        $user = User::load($data['uid']);
        if ($user) {
          $first_name = trim(ucwords($user->field_first_name->getValue()[0]['value']));
          $to_email = $user->getEmail();
          // var_dump($first_name);
          // var_dump($to_email);
          // exit;
          $subject = "Svar fra Castit! / Response from Castit! ";
          $html_body = <<< EOM
          <p>Kære $first_name</p>
          <p>Mange tak for din ansøgning som profil hos Castit. Vi sætter stor pris på din interesse.</p>

          <p>Vi kan dog desværre ikke tilbyde dig en Online profil – men vi gemmer din ansøgning og vi vil selvfølgelig foreslå dig til relevante jobs.</p>

          <p>Vi ønsker dig held og lykke fremover.</p>

          <p>De bedste hilsner</p>

          <p>Cathrine & Pernille</p>
          <p></p>
          <br/>
          <img style="max-width: 150px;" src="https://castit.dk/images/new_logo_black.png" alt="" />
          <p>Rosenvængets Allè 11, 1. Sal</p>
          <p>2100 København Ø</p>


          <p>Cathrine Hovmand</p>
          <p># 0045 2128 5825</p>
          <p>E: cat@castit.dk</p>

          <p>Pernille Marco: </p>
          <p># 0045 3135 3579</p>
          <p>E: pernille@castit.dk</p>

          <p><a href="https://castit.dk">castit.dk</a></p>

          -------------------------------------------------------------------------------------------------------


          <p>Dear $first_name</p>
          <p>Thank you for your application as a profile at Castit. We appreciate your interest.</p>
          <p>Unfortunately, we cannot offer you an online profile - but we saved your application and we will of course suggest you for relevant jobs.</p>

          <p>We wish you all the best.</p>

          <p>Very best</p>

          <p>Cathrine & Pernille</p>
          <p></p>
          <br/>
          <img style="max-width: 150px;" src="https://castit.dk/images/new_logo_black.png" alt="" />
          <p>Rosenvængets Allè 11, 1st floor</p>
          <p>2100 Copenhagen Ø</p>

          <p>Cathrine Hovmand</p>
          <p># 0045 2128 5825</p>
          <p>E: cat@castit.dk</p>

          <p>Pernille Marco:</p>
          <p># 0045 3135 3579</p>
          <p>E: pernille@castit.dk</p>

          <p><a href="https://castit.dk">www.castit.dk</a></p>
EOM;
          // $to_email = 'padmanabhann1@mailinator.com';
          $mgClient = new Mailgun('key-ebe8829c00330a3be43c59dd67da5b73');
          $domain = "mail.castit.dk";
          $result = $mgClient->sendMessage(
            $domain,
            [
              'from'    => 'CASTIT <info@castit.dk>',
              'to'      => $to_email,
              'subject' => $subject,
              'html'    => $html_body,
              'bcc'	=> 'padmanabhann@mailinator.com',
            ]);
          $response['message'] = 'Email er sendt!';
          $response['email'] = $to_email;
        }
      }
    }
    return new JsonResponse( $response );
  }
}
