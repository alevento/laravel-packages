<?php

/**
 * Created by PhpStorm.
 * User: Alessandro
 * Date: 01/12/2015
 * Time: 12:37
 */

use GuzzleHttp\Psr7\Response;

class MailTestCase extends TestCase
{
    protected $mailcatcher;

    function __construct()
    {
        $this->mailcatcher = new \GuzzleHttp\Client(['base_uri' => 'http://192.168.0.29:1080']);
    }

    public function getAllEmails()
    {
        $emails = json_decode($this->mailcatcher->get('/messages')->getBody()->getContents(),true);
        if(empty($emails)) {
            $this->fail('No messages returned.');
        }

        return $emails;
    }

    public function deleteAllMails()
    {
        return $this->mailcatcher->delete('/messages');
    }

    public function getLastEmailHtml()
    {
        $emailId = $this->getAllEmails()[0]['id'];
        return $this->mailcatcher->get("/messages/{$emailId}.html");
    }

    public function getEmailById($id)
    {

        foreach($this->getAllEmails() as $email) {
            if ($email['id']==$id) {
                return $email;
            }
        }
    }

    public function getLastEmailJson()
    {
        //return $this->getAllEmails()[0];
        $emailId = $this->getAllEmails()[0]['id'];
        return $this->mailcatcher->get("/messages/{$emailId}.json");

    }

    public function assertEmailBodyContains($body, Response $email)
    {
        $this->assertContains($body,(string)$email->getBody());
    }

    public function assertNotEmailBodyContains($body, Response $email)
    {
        $this->assertNotContains($body,(string)$email->getBody());
    }

    public function assertEmailWasSentoTo($recipient, Response $email)
    {
        $emailDecode = json_decode($email->getBody()->getContents(),true);
        $this->assertContains ("<{$recipient}>", $emailDecode['recipients']);
    }

    public function assertNotEmailWasSentoTo($recipient, $email)
    {
        $this->assertNotContains ("<{$recipient}>", $email['recipients']);
    }
}
