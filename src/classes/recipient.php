<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * TDM: Module modification notification.
 *
 * @package   local_tdmmodnotify
 * @author    Luke Carrier <luke@tdm.co>
 * @copyright (c) 2014 The Development Manager Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Recipient.
 */
class local_tdmmodnotify_recipient extends local_tdmmodnotify_model {
    /**
     * User ID.
     *
     * @var integer
     */
    protected $userid;

    /**
     * User forename.
     *
     * @var string
     */
    protected $userfirstname;

    /**
     * User surname.
     *
     * @var string
     */
    protected $userlastname;

    /**
     * Notifications.
     *
     * @var local_tdmmodnotify_notification[]
     */
    protected $notifications;

    /**
     * Initialiser.
     *
     * @param integer                           $userid        User ID.
     * @param string                            $userfirstname User forename.
     * @param string                            $userlastname  User surname.
     * @param local_tdmmodnotify_notification[] $notifications Notifications.
     */
    public function __construct($userid, $userfirstname, $userlastname, $notifications) {
        $this->userid        = $userid;
        $this->userfirstname = $userfirstname;
        $this->userlastname  = $userlastname;

        $this->notifications = $notifications;
    }

    /**
     * Build the notification content.
     *
     * @param stdClass $substitutions The string substitions to be passed to the location API when generating the
     *                                content. If this recipient object will have contain notifications, this object
     *                                must include a moodle_url object in its baseurl property else a fatal error will
     *                                be raised when building their content.
     *
     * @return string The notification content.
     */
    public function build_content($substitutions) {
        $resourcelist = '';
        foreach ($this->notifications as $notification) {
            $resourcelist .= $notification->build_content($substitutions);
        }

        return substr($resourcelist, 0, -1);
    }

    /**
     * Delete the recipient's record.
     *
     * @return void
     */
    public function delete() {
        global $DB;

        $DB->delete_records('local_tdmmodnotify', array(
            'userid' => $this->userid,
        ));
    }

    /**
     * @override \local_tdmmodnotify_model
     */
    public function model_accessors() {
        return array(
            'userid',
            'userfirstname',
            'userlastname',
            'notifications',
        );
    }

    /**
     * Build a recipient object and child notification objects from a digest.
     *
     * @param stdClass $notificationdigest A notfication digest object from the DML API.
     *
     * @return \local_tdmmodnotify_recipient A recipient object.
     */
    public static function from_digest($notificationdigest) {
        $notification  = current($notificationdigest);
        $userid        = $notification->userid;
        $userfirstname = $notification->userfirstname;
        $userlastname  = $notification->userlastname;

        $notifications = array();
        foreach ($notificationdigest as $notification) {
            $notifications[] = local_tdmmodnotify_notification::from_digest($notification);
        }

        return new static($userid, $userfirstname, $userlastname, $notifications);
    }
}
