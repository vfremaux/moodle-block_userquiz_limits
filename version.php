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
 * Version details.
 *
 * @package     block_userquiz_limits
 * @category    blocks
 * @author      valery.fremaux <valery.fremaux@gmail.com>
 * @copyright   2013 onwards Valery Fremaux (http://www.mylearningfactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

<<<<<<< HEAD
$plugin->version   = 2018011500; // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2017050500; // Requires this Moodle version.
$plugin->component = 'block_userquiz_limits'; // Full name of the plugin (used for diagnostics).
$plugin->maturity = MATURITY_BETA;
$plugin->release = "3.3 (Build 2018011500)";
$plugin->dependencies = array('quizaccess_usernumattempts' => 2016062300);

// Non moodle attributes.
$plugin->codeincrement = '3.3.0001';
=======
$plugin->version   = 2018011800; // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2018042700; // Requires this Moodle version.
$plugin->component = 'block_userquiz_limits'; // Full name of the plugin (used for diagnostics).
$plugin->maturity = MATURITY_RC;
$plugin->release = '3.5.0 (Build 2018011800)';
$plugin->dependencies = array('quizaccess_usernumattempts' => 2016062300);

// Non moodle attributes.
<<<<<<< HEAD
$plugin->codeincrement = '3.5.0002';
>>>>>>> MOODLE_35_STABLE
=======
$plugin->codeincrement = '3.5.0003';
>>>>>>> MOODLE_35_STABLE
