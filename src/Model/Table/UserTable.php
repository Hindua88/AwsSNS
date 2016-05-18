<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * User Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Invites
 * @property \Cake\ORM\Association\HasMany $LogLogin
 * @property \Cake\ORM\Association\HasMany $LogPoint
 * @property \Cake\ORM\Association\HasMany $SurveyUserLog
 * @property \Cake\ORM\Association\HasMany $UserAdcolony
 * @property \Cake\ORM\Association\HasMany $UserAppdriver
 * @property \Cake\ORM\Association\HasMany $UserBackup
 * @property \Cake\ORM\Association\HasMany $UserBet
 * @property \Cake\ORM\Association\HasMany $UserBlackList
 * @property \Cake\ORM\Association\HasMany $UserDailyTicket
 * @property \Cake\ORM\Association\HasMany $UserFacebook
 * @property \Cake\ORM\Association\HasMany $UserInfo
 * @property \Cake\ORM\Association\HasMany $UserInvite
 * @property \Cake\ORM\Association\HasMany $UserOpenLink
 * @property \Cake\ORM\Association\HasMany $UserOpenapp
 * @property \Cake\ORM\Association\HasMany $UserPartyTrack
 * @property \Cake\ORM\Association\HasMany $UserPhone
 * @property \Cake\ORM\Association\HasMany $UserQuestion
 * @property \Cake\ORM\Association\HasMany $UserReview
 * @property \Cake\ORM\Association\HasMany $UserShareLink
 * @property \Cake\ORM\Association\HasMany $UserSnsEndpoint
 * @property \Cake\ORM\Association\HasMany $UserSponsorpay
 * @property \Cake\ORM\Association\HasMany $UserSupersonic
 * @property \Cake\ORM\Association\HasMany $UserVungle
 * @property \Cake\ORM\Association\BelongsToMany $Garena
 * @property \Cake\ORM\Association\BelongsToMany $Gatecard
 * @property \Cake\ORM\Association\BelongsToMany $Gmobile
 * @property \Cake\ORM\Association\BelongsToMany $Mobifone
 * @property \Cake\ORM\Association\BelongsToMany $Vcoin
 * @property \Cake\ORM\Association\BelongsToMany $Vietnamobile
 * @property \Cake\ORM\Association\BelongsToMany $Viettel
 * @property \Cake\ORM\Association\BelongsToMany $Vinagame
 * @property \Cake\ORM\Association\BelongsToMany $Vinaphone
 */
class UserTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('user');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Invites', [
            'foreignKey' => 'invite_id'
        ]);
        $this->hasMany('LogLogin', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('LogPoint', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('SurveyUserLog', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserAdcolony', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserAppdriver', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserBackup', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserBet', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserBlackList', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserDailyTicket', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserFacebook', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserInfo', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserInvite', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserOpenLink', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserOpenapp', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserPartyTrack', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserPhone', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserQuestion', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserReview', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserShareLink', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserSnsEndpoint', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserSponsorpay', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserSupersonic', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UserVungle', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsToMany('Garena', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'garena_id',
            'joinTable' => 'user_garena'
        ]);
        $this->belongsToMany('Gatecard', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'gatecard_id',
            'joinTable' => 'user_gatecard'
        ]);
        $this->belongsToMany('Gmobile', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'gmobile_id',
            'joinTable' => 'user_gmobile'
        ]);
        $this->belongsToMany('Mobifone', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'mobifone_id',
            'joinTable' => 'user_mobifone'
        ]);
        $this->belongsToMany('Vcoin', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'vcoin_id',
            'joinTable' => 'user_vcoin'
        ]);
        $this->belongsToMany('Vietnamobile', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'vietnamobile_id',
            'joinTable' => 'user_vietnamobile'
        ]);
        $this->belongsToMany('Viettel', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'viettel_id',
            'joinTable' => 'user_viettel'
        ]);
        $this->belongsToMany('Vinagame', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'vinagame_id',
            'joinTable' => 'user_vinagame'
        ]);
        $this->belongsToMany('Vinaphone', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'vinaphone_id',
            'joinTable' => 'user_vinaphone'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('uuid');

        $validator
            ->integer('point')
            ->requirePresence('point', 'create')
            ->notEmpty('point');

        $validator
            ->allowEmpty('phone_confirm');

        $validator
            ->integer('confirm_sms_flag')
            ->requirePresence('confirm_sms_flag', 'create')
            ->notEmpty('confirm_sms_flag');

        $validator
            ->dateTime('time_confirm')
            ->allowEmpty('time_confirm');

        $validator
            ->date('last_time_get_point')
            ->allowEmpty('last_time_get_point');

        $validator
            ->allowEmpty('token');

        $validator
            ->boolean('first_launch')
            ->allowEmpty('first_launch');

        $validator
            ->integer('finish_tutorial_flag')
            ->requirePresence('finish_tutorial_flag', 'create')
            ->notEmpty('finish_tutorial_flag');

        $validator
            ->integer('block_flag')
            ->allowEmpty('block_flag');

        $validator
            ->integer('block_invite_flag')
            ->allowEmpty('block_invite_flag');

        $validator
            ->integer('user_use_invite')
            ->allowEmpty('user_use_invite');

        $validator
            ->integer('number_input_invite')
            ->requirePresence('number_input_invite', 'create')
            ->notEmpty('number_input_invite');

        $validator
            ->date('last_date_login')
            ->allowEmpty('last_date_login');

        $validator
            ->integer('exchange_point_flag')
            ->requirePresence('exchange_point_flag', 'create')
            ->notEmpty('exchange_point_flag');

        $validator
            ->integer('point_add')
            ->requirePresence('point_add', 'create')
            ->notEmpty('point_add');

        $validator
            ->integer('show_icon_campaign')
            ->requirePresence('show_icon_campaign', 'create')
            ->notEmpty('show_icon_campaign');

        $validator
            ->integer('token_error')
            ->requirePresence('token_error', 'create')
            ->notEmpty('token_error');

        $validator
            ->allowEmpty('error_name');

        $validator
            ->allowEmpty('idfa');

        $validator
            ->integer('sent_party_track')
            ->requirePresence('sent_party_track', 'create')
            ->notEmpty('sent_party_track');

        $validator
            ->integer('step_tutorial')
            ->requirePresence('step_tutorial', 'create')
            ->notEmpty('step_tutorial');

        $validator
            ->boolean('open_link_get_point_flag')
            ->allowEmpty('open_link_get_point_flag');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['invite_id'], 'Invites'));
        return $rules;
    }


    public function getTokens($limit = 1000, $page = 1)
    {
        $query = $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'token'
            ])->where(['token is not null'])->limit($limit)->page($page);

        return $query->toArray();
    }
}
