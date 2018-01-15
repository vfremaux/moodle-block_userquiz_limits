<?php

require_once('test_client_base.php');

class test_client extends test_client_base {

    public function test_get_user_quiz_limits($userfield, $users, $quizfield, $quizzes) {

        if (empty($this->t->baseurl)) {
            echo "Test target not configured\n";
            return;
        }

        if (empty($this->t->wstoken)) {
            echo "No token to proceed\n";
            return;
        }

        $params = array('wstoken' => $this->t->wstoken,
                        'wsfunction' => 'block_userquiz_limits_get_user_quiz_limits',
                        'moodlewsrestformat' => 'json',
                        'userfield' => $userfield,
                        'users' => $users,
                        'quizfield' => $quizfield,
                        'quizzes' => $quizzes);

        $serviceurl = $this->t->baseurl.$this->t->service;

        return $this->send($serviceurl, $params);
    }

    public function test_set_user_quiz_limits($userfield, $users, $quizfield, $quizzes, $value) {

        if (empty($this->t->baseurl)) {
            echo "Test target not configured\n";
            return;
        }

        if (empty($this->t->wstoken)) {
            echo "No token to proceed\n";
            return;
        }

        $params = array('wstoken' => $this->t->wstoken,
                        'wsfunction' => 'block_userquiz_limits_set_user_quiz_limits',
                        'moodlewsrestformat' => 'json',
                        'userfield' => $userfield,
                        'users' => $users,
                        'quizfield' => $quizfield,
                        'quizzes' => $quizzes,
                        'limit' => $value);

        $serviceurl = $this->t->baseurl.$this->t->service;

        return $this->send($serviceurl, $params);
    }

    public function test_add_user_quiz_limits($userfield, $users, $quizfield, $quizzes, $value) {

        if (empty($this->t->baseurl)) {
            echo "Test target not configured\n";
            return;
        }

        if (empty($this->t->wstoken)) {
            echo "No token to proceed\n";
            return;
        }

        $params = array('wstoken' => $this->t->wstoken,
                        'wsfunction' => 'block_userquiz_limits_add_user_quiz_limits',
                        'moodlewsrestformat' => 'json',
                        'userfield' => $userfield,
                        'users' => $users,
                        'quizfield' => $quizfield,
                        'quizzes' => $quizzes,
                        'limit' => $value);

        $serviceurl = $this->t->baseurl.$this->t->service;

        return $this->send($serviceurl, $params);
    }

}

// Effective test scenario.

$userfield = 'username';
$quizfield = 'cmid';
$users = array('a1', 'a2', 'a3', 'a4', 'b1', 'b2', 'b3', 'b4', 'c1', 'c2');
$quizzes = array(53);
$limittoset1 = 4;
$limittoadd = 5;
$limittoset2 = 20;

echo "STARTING:\n";
$client = new test_client();

echo "TEST GETTING STARTING VALUES:\n";
$results = $client->test_get_user_quiz_limits($userfield, $users, $quizfield, $quizzes);
print_r($results);

echo "TEST SETTING LIMITS 1:\n";
$result = $client->test_set_user_quiz_limits($userfield, $users, $quizfield, $quizzes, $limittoset1);
print_r($results);

echo "TEST ADDING LIMITS 1:\n";
$result = $client->test_add_user_quiz_limits($userfield, $users, $quizfield, $quizzes, $limittoadd);

echo "TEST CHECKING LIMIT STATES:\n";
$results = $client->test_get_user_quiz_limits($userfield, $users, $quizfield, $quizzes);
print_r($results);

echo "TEST SETTING LIMITS 2:\n";
$result = $client->test_set_user_quiz_limits($userfield, $users, $quizfield, $quizzes, $limittoset2);
print_r($results);

echo "TEST CHECKING FINAL LIMIT STATES:\n";
$results = $client->test_get_user_quiz_limits($userfield, $users, $quizfield, $quizzes);
print_r($results);
