<?php

namespace Drupal\castit_user_import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
/**
 * Class DefaultController.
 */
class DefaultController extends ControllerBase {

  /**
   * Import.
   *
   * @return string
   *   Return Hello string.
   */
  public function import($endIndex) {
    include 'ModelsData.php';
    // echo count($models);
    foreach($models as $main_key => $model){
      // if($main_key > 0 && $main_key <= 500){
      // if($main_key > 500 && $main_key <= 1000){
      // if($main_key > 1000 && $main_key <= 2000){
      // if($main_key > 1500 && $main_key <= 2000){
      // if($main_key > 2000 && $main_key <= 2500){
      // if($main_key > 2500 && $main_key <= 3000){
      // if($main_key > 3000 && $main_key <= 3500){
      // if($main_key > 3500 && $main_key <= 4000){
      // if($main_key > 4000 && $main_key <= 4500){

        switch ($endIndex) {
          case '1':
            if($main_key >= 0 && $main_key <= 1){$this->user_create($model);}
          break;
          case '500':
          default:
            if($main_key >= 0 && $main_key <= 500){$this->user_create($model);}
          break;
          case '1000':
            if($main_key > 500 && $main_key <= 1000){$this->user_create($model);}
          break;
          case '1500':
            if($main_key > 1000 && $main_key <= 1500){$this->user_create($model);}
          break;
          case '2000':
            if($main_key > 1500 && $main_key <= 2000){$this->user_create($model);}
          break;
          case '2500':
            if($main_key > 2000 && $main_key <= 2500){$this->user_create($model);}
          break;
          case '3000':
            if($main_key > 2500 && $main_key <= 3000){$this->user_create($model);}
          break;
          case '3500':
            if($main_key > 3000 && $main_key <= 3500){$this->user_create($model);}
          break;
          case '4000':
            if($main_key > 3500 && $main_key <= 4000){$this->user_create($model);}
          break;
          case '4500':
            if($main_key > 4000 && $main_key <= 4500){$this->user_create($model);}
          break;
          case '5000':
            if($main_key > 4500 && $main_key <= 5000){$this->user_create($model);}
          break;

          // default:
            // # code...
            // break;
        }
        continue;
    }

    // echo '<pre>';
    // echo '**';
    // print_r(User::load(565));

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implemented method: import')
    ];
  }

  public function user_create($model) {
    unset($user);
    foreach($model['payments'] as $pay_key => $pay){
      $payment_import_array[]['index'] = '';
      $payment_type_id = $pay['payment_type_id'];
      $paid = $pay['paid'];
      $applies = $pay['applies'];
      $description = $pay['description'];
      $payment_import_array[]['data'] = '{"index":"","0":"'.$payment_type_id.'","1":"'.$applies.'","2":"'.$paid.'","3":"'.$description.'"}';
      }
    $user = entity_create('user', [
      'name' => $model['email'],
      'pass' => $model['pass'],
      // 'pass' => 'pass@123',
      'mail' => $model['email'],
      'field_about_me' => $model['notes'],
      'field_agreed_to_terms' => isset($model['agreed_to_these_terms'])? $model['agreed_to_these_terms'] : 0,
      'field_category' => $model['categories'],
      'field_cellphone' => $model['cellphone'],
      'field_dialect_one' => $model['dealekter1'],
      'field_dialect_three' => $model['dealekter3'],
      'field_dialect_two' => $model['dealekter2'],
      'field_ethnic_origin' => $model['ethnic_origin'],
      'field_fax' => $model['fax'],
      'field_language_four' => isset($model['lang'][3])? $model['lang'][3]['lang_id'] : '',
      'field_language_four_rating' => isset($model['lang'][3])? $model['lang'][3]['rating'] : '',
      'field_language_one' => isset($model['lang'][0])? $model['lang'][0]['lang_id'] : '',
      'field_language_one_rating' => isset($model['lang'][0])? $model['lang'][0]['rating'] : '',
      'field_language_three' => isset($model['lang'][2])? $model['lang'][2]['lang_id'] : '',
      'field_language_three_rating' => isset($model['lang'][2])? $model['lang'][2]['rating'] : '',
      'field_language_two' => isset($model['lang'][1])? $model['lang'][1]['lang_id'] : '',
      'field_language_two_rating' => isset($model['lang'][1])? $model['lang'][1]['rating'] : '',
      'field_licenses' => $model['licenses'],
      'field_nationality' => $model['nationality'],
      'field_new_from' => (isset($model['marked_as_new_from']))? strtotime($model['marked_as_new_from']) : '',
      'field_new_profile' => $model['marked_as_new'],
      'field_new_until' => (isset($model['marked_as_new_till']))? strtotime($model['marked_as_new_till']) : '',
      'field_old_profile_id' => $model['id'],
      'field_payments' => $payment_import_array,
      'field_photos' => $model['pics'],
      'field_photo_thumbnails' => $model['pics_thumbs'],
      'field_profile_number' => $model['profile_number'],
      'field_old_profile_number' => $model['profile_number'],
      'field_profile_status' => $model['profile_status_id'],
      'field_profile_type' => $model['profile_type'],
      'field_recently_updated' => $model['recently_updated'],
      'field_skills' => $model['skills'],
      'field_sports_and_hobby' => $model['sports_hobby'],
      'field_suit_size_from' => $model['suite_size_from'],
      'field_suit_size_to' => $model['suite_size_to'],
      'field_shoe_size_from' => $model['shoe_size_from'],
      'field_shoe_size_to' => $model['shoe_size_to'],
      'field_shirt_size_from' => $model['shirt_size_from'],
      'field_shirt_size_to' => $model['shirt_size_to'],
      'field_pant_size_from' => $model['pants_size_from'],
      'field_pant_size_to' => $model['pants_size_to'],
      'field_telephone' => [$model['phone'],$model['phone_at_work']],
      'field_videos' => $model['vids'],
      'field_video_thumbnails' => $model['thumbs'],
      'field_first_name' => $model['first_name'],
      'field_last_name' => $model['last_name'],
      'field_birthday' => $model['birthday'],
      'field_country' => $model['country_id'],
      'field_gender' => $model['gender_id'],
      'field_occupation' => $model['job'],
      'field_hair_color' => $model['hair_color_id'],
      'field_eye_color' => $model['eye_color_id'],
      'field_height' => $model['height'],
      'field_weight' => $model['weight'],
      'created' => strtotime($model['created_at']),
      'changed' => strtotime($model['updated_at']),
      'field_zipcode' => $model['zipcode'],
      'field_address' => $model['address'],
      'field_bureau' => $model['bureau'],
      'field_city' => $model['city'],
      'field_new_from' => $model['marked_as_new_from'],
      'field_new_until' => $model['marked_as_new_till'],
      'field_new_profile' => $model['marked_as_new'],
      'field_bra_size' => $model['bra_size']

    ]);
    $user->addRole('model');
    $user->activate();
    $user->save();
    // $gender = ($model['gender_id'] == 1) ? 'M' : 'F';
    // $profileNumber = $model['profile_type'] . $gender . str_pad($user->id(), 4, "0", STR_PAD_LEFT);
    // $user->set('field_profile_number', $profileNumber);
    // $user->save();
    return 'true';
  }

  public function updateoldprofiles() {

  }
  public function update_password() {
    include 'old-passwords.php';
    foreach($passwords as $profile) {
      $user = User::load($profile['uid']);
      $user->setPassword($profile['password']);
      $user->save();
    }
  }
}
