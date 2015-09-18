<?php defined('APPLICATION') or die;

$PluginInfo['Nickname'] = array(
    'Name' => 'Nickname',
    'Description' => 'Users can enter a nickname which will be used instead of the normal username. This plugin is based on an <a href="http://vanillaforums.org/addon/870/firstlastnames">idea</a> of <a href="http://vanillaforums.org/profile/jspautsch">Jonathan Pautsch</a>.',
    'Version' => '0.1',
    'RequiredApplications' => array('Vanilla' => '>= 2.1'),
    'RequiredPlugins' => array('ProfileExtender' => '>= 3'),
    'RequiredTheme' => false,
    'SettingsPermission' => 'Garden.Settings.Manage',
    'SettingsUrl' => '/dashboard/settings/nickname',
    'MobileFriendly' => true,
    'HasLocale' => false,
    'Author' => 'Robin Jurinka',
    'AuthorUrl' => 'http://vanillaforums.org/profile/44046/R_J',
    'License' => 'MIT'
);



/**
 * Plugin that shows a nickname on various places.
 *
 * @package Nickname
 * @author Robin Jurinka
 * @license MIT
 */
class NicknamePlugin extends Gdn_Plugin {

// Dummy!
public function setup() {
    if (!c('Nickname.Fieldname')) {
        saveToConfig('Nickname.Fieldname', 'Nickname');
    }
}


    /**
     * Choose the profile extender field that should be used as nickname.
     *
     * @param  object $sender SettingsController.
     * @return void.
     * @package Nickname
     * @since 0.1
     */
    public function settingsController_nickname_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        $sender->addSideMenu('dashboard/settings/plugins');

        $sender->title(t('Nickname Settings'));
        $sender->description(t('Nickname Settings'));
    }



    public function discussionController_AuthorInfo_handler($sender, $args) {
        echo '<style>.Nickname > a:before {content: "@";}</style><span class="Nickname">(@'.$args['Author']->Name.')</span>';

        // get all fields since that is better for caching!
        // $profileFields = Gdn::userMetaModel()->getUserMeta($args['Author']->UserID, 'Profile.%', 'Profile.');
        // $nickname = $profileFields['Profile.'.c('Nickname.Fieldname')];

    }

    public function profileController_usernameMeta_handler($sender, $args) {
        // get all fields since that is better for caching!
        $profileFields = Gdn::userMetaModel()->getUserMeta($sender->User->UserID, 'Profile.%', 'Profile.');
        $nickname = $profileFields['Profile.'.c('Nickname.Fieldname')];
        if ($nickname) {
            echo '<span class="Nickname">(a.k.a. '.$nickname.')</span>';
        }
    }

}

if (!function_exists('userAnchor')) {
    /**
     * Take a user object, and writes out an anchor of the user's name to the user's profile.
     */
    function userAnchor($User, $CssClass = null, $Options = null) {
        static $NameUnique = null;
        if ($NameUnique === null) {
            $NameUnique = c('Garden.Registration.NameUnique');
        }

        if (is_array($CssClass)) {
            $Options = $CssClass;
            $CssClass = null;
        } elseif (is_string($Options)) {
            $Options = array('Px' => $Options);
        }

        $Px = GetValue('Px', $Options, '');

        $Name = GetValue($Px.'Name', $User, t('Unknown'));
        // $Text = GetValue('Text', $Options, htmlspecialchars($Name)); // Allow anchor text to be overridden.
$Text = GetValue('Text', $Options, '');
if ($Text == '') {
    // get all fields since that is better for caching!
    $profileFields = Gdn::userMetaModel()->getUserMeta($User->UserID, 'Profile.%', 'Profile.');
    $Text = $profileFields['Profile.'.c('Nickname.Fieldname')];
        if ($Text == '') {
            $Text = htmlspecialchars($Name);
        }
    // return
} else {
    $Text = htmlspecialchars($Name);
}
        $Attributes = array(
            'class' => $CssClass,
            'rel' => GetValue('Rel', $Options)
        );
        if (isset($Options['title'])) {
            $Attributes['title'] = $Options['title'];
        }
        $UserUrl = UserUrl($User, $Px);

        return '<a href="'.htmlspecialchars(Url($UserUrl)).'"'.Attribute($Attributes).'>'.$Text.'</a>';
    }
}
