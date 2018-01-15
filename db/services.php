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
 * Quiz individual limits.
 *
 * @package    block_userquiz_limits
 * @category   external
 * @copyright  2017 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'block_userquiz_limits_get_user_quiz_limits' => array(
        'classname'     => 'block_userquiz_limits_external',
        'methodname'    => 'get_user_quiz_limits',
        'description'   => 'Returns the limit for a set of users and quizes',
        'type'          => 'read',
        'capabilities'  => ''
    ),
    'block_userquiz_limits_set_user_quiz_limits' => array(
        'classname'     => 'block_userquiz_limits_external',
        'methodname'    => 'set_user_quiz_limits',
        'description'   => 'Set the limit for a set of users and quizes',
        'type'          => 'write',
        'capabilities'  => ''
    ),
    'block_userquiz_limits_add_user_quiz_limits' => array(
        'classname'     => 'block_userquiz_limits_external',
        'methodname'    => 'add_user_quiz_limits',
        'description'   => 'Adds some limit units to a set of users and quizes',
        'type'          => 'write',
        'capabilities'  => ''
    ),
);