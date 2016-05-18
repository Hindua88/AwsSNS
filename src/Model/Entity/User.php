<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity.
 *
 * @property int $id
 * @property string $uuid
 * @property int $point
 * @property string $phone_confirm
 * @property int $confirm_sms_flag
 * @property \Cake\I18n\Time $time_confirm
 * @property \Cake\I18n\Time $last_time_get_point
 * @property string $token
 * @property bool $first_launch
 * @property int $finish_tutorial_flag
 * @property int $block_flag
 * @property int $block_invite_flag
 * @property string $invite_id
 * @property \App\Model\Entity\Invite $invite
 * @property int $user_use_invite
 * @property int $number_input_invite
 * @property \Cake\I18n\Time $last_date_login
 * @property int $exchange_point_flag
 * @property int $point_add
 * @property int $show_icon_campaign
 * @property int $token_error
 * @property string $error_name
 * @property string $idfa
 * @property int $sent_party_track
 * @property int $step_tutorial
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property bool $open_link_get_point_flag
 * @property \App\Model\Entity\LogLogin[] $log_login
 * @property \App\Model\Entity\LogPoint[] $log_point
 * @property \App\Model\Entity\SurveyUserLog[] $survey_user_log
 * @property \App\Model\Entity\UserAdcolony[] $user_adcolony
 * @property \App\Model\Entity\UserAppdriver[] $user_appdriver
 * @property \App\Model\Entity\UserBackup[] $user_backup
 * @property \App\Model\Entity\UserBet[] $user_bet
 * @property \App\Model\Entity\UserBlackList[] $user_black_list
 * @property \App\Model\Entity\UserDailyTicket[] $user_daily_ticket
 * @property \App\Model\Entity\UserFacebook[] $user_facebook
 * @property \App\Model\Entity\UserInfo[] $user_info
 * @property \App\Model\Entity\UserInvite[] $user_invite
 * @property \App\Model\Entity\UserOpenLink[] $user_open_link
 * @property \App\Model\Entity\UserOpenapp[] $user_openapp
 * @property \App\Model\Entity\UserPartyTrack[] $user_party_track
 * @property \App\Model\Entity\UserPhone[] $user_phone
 * @property \App\Model\Entity\UserQuestion[] $user_question
 * @property \App\Model\Entity\UserReview[] $user_review
 * @property \App\Model\Entity\UserShareLink[] $user_share_link
 * @property \App\Model\Entity\UserSnsEndpoint[] $user_sns_endpoint
 * @property \App\Model\Entity\UserSponsorpay[] $user_sponsorpay
 * @property \App\Model\Entity\UserSupersonic[] $user_supersonic
 * @property \App\Model\Entity\UserVungle[] $user_vungle
 * @property \App\Model\Entity\Garena[] $garena
 * @property \App\Model\Entity\Gatecard[] $gatecard
 * @property \App\Model\Entity\Gmobile[] $gmobile
 * @property \App\Model\Entity\Mobifone[] $mobifone
 * @property \App\Model\Entity\Vcoin[] $vcoin
 * @property \App\Model\Entity\Vietnamobile[] $vietnamobile
 * @property \App\Model\Entity\Viettel[] $viettel
 * @property \App\Model\Entity\Vinagame[] $vinagame
 * @property \App\Model\Entity\Vinaphone[] $vinaphone
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * Fields that are excluded from JSON an array versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token'
    ];
}
