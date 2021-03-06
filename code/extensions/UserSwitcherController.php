<?php
/**
 * UserSwitcherController
 *
 * @author Shea Dawson <shea@livesource.co.nz>
 * @license BSD http://silverstripe.org/bsd-license/
 */
class UserSwitcherController extends Controller {

	Const URLSegment = 'userswitcher';

	public static $allowed_actions = array(
		'UserSwitcherForm',
	);

	public function UserSwitcherForm() {
		if(Director::isLive()){
			return false;
		}

		if(Permission::check('ADMIN') || Session::get('UserSwitched')) {
			$members = Member::get()->map()->toArray();
				
			if(isset($_GET['userswitchercms']) && $_GET['userswitchercms'] == 1) {
				$field = DropdownField::create('MemberID', '', $members)
					->setEmptyString(_t('UserSwticherController.SwitchUser','Switch User'));
			} else {
				$field = DropdownField::create('MemberID', 'User:', $members, Member::currentUserID());
			}

			$fields = FieldList::create($field);

			$actions = FieldList::create(
				FormAction::create('switchuser', 'Switch User')
			);

			$validator = RequiredFields::create(
				'MemberID'
			);

			return Form::create($this, 'UserSwitcherForm', $fields, $actions, $validator)
				->addExtraClass('userswitcher');
		}
	}

	public function switchuser($data, $form) {
		if(Permission::check('ADMIN') || Session::get('UserSwitched')){	
			if($member = Member::get()->byID((int)$data['MemberID'])) {
				$member->logIn();
				Session::set('UserSwitched', 1);
				return $this->redirectBack();
			}
		} else {
			return $this->httpError('404');
		}
	}

	public function Link($action = '') {
		return self::URLSegment . '/' . $action;
	}

}