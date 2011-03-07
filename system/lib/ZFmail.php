<?php
/**
 * ZFmail
 *
 * A (very) simple mailer class written in PHP.
 *
 * @author Zachary Fox
 * @author Andrey A Antonov — UTF adapt
 * @version 1.1
 */

class ZFmail {
	var $to = null;
	var $from = null;
	var $subject = null;
	var $body = null;
	var $headers = null;
	
	function ZFmail($to, $from, $subject, $body) {
		$this->to = $to;
		$this->from = $from;
		$this->subject = $subject;
		$this->body = $body;
	}
	
	function send() {
		$this->addHeader ( 'From: ' . $this->from . "\r\n" );
		$this->addHeader ( 'Reply-To: ' . $this->from . "\r\n" );
		$this->addHeader ( 'Return-Path: ' . $this->from . "\r\n" );
		$this->addHeader ( 'Content-Type: text/plain; charset=UTF-8' . "\r\n" );
		$this->addHeader ( 'Content-Transfer-Encoding: 8bit' . "\r\n" );
		return mail ( $this->to, '=?UTF-8?B?'.base64_encode($this->subject).'?=', $this->body, $this->headers );
	}
	
	function addHeader($header) {
		$this->headers .= $header;
	}

}