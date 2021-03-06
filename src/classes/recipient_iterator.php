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
 * Recipient iterator.
 */
class local_tdmmodnotify_recipient_iterator implements Iterator {
    /**
     * Record set.
     *
     * @var stdClass[]
     */
    protected $records;

    /**
     * Initialiser.
     */
    public function __construct() {
        $this->records = local_tdmmodnotify_util::get_scheduled_recipients();
    }

    /**
     * Return a recipient object for the current recipient.
     *
     * @return local_tdmmodnotify_recipient The recipient object.
     */
    public function current() {
        $userid        = current($this->records);
        $notifications = local_tdmmodnotify_util::get_notification_digest($userid);

        return local_tdmmodnotify_recipient::from_digest($notifications);
    }

    /**
     * Get the current recipient key.
     *
     * @return integer The ID of the recipient's associated user.
     */
    public function key() {
        return key($this->records);
    }

    /**
     * Skip to the next record.
     *
     * @return void
     */
    public function next() {
        next($this->records);
    }

    /**
     * Rewind to the beginning of the record set.
     *
     * @return void
     */
    public function rewind() {
        reset($this->records);
    }

    /**
     * Does the iterator still have records remaining?
     *
     * @return boolean True if records remain, else false.
     */
    public function valid() {
        return current($this->records) !== false;
    }
}
