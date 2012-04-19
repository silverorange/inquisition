<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Site/dataobjects/SiteAccount.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionResponseWrapper.php';

/**
 * An inquisition
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisition extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var SwatDate
	 */
	public $createdate;

	// }}}
	// {{{ public function getResponseByAccount()

	public function getResponseByAccount(SiteAccount $account)
	{
		$this->checkDB();

		$sql = sprintf('select * from InquisitionResponse
			where account = %s and inquisition = %s',
			$this->db->quote($account->id, 'integer'),
			$this->db->quote($this->id, 'integer'));

		$wrapper  = SwatDBClassMap::get('InquisitionResponseWrapper');
		$response = SwatDB::query($this->db, $sql, $wrapper)->getFirst();

		if ($response !== null) {
			$response->inquisition = $this;
		}

		return $response;
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'Inquisition';
		$this->id_field = 'integer:id';
		$this->registerDateProperty('createdate');
	}

	// }}}
	// {{{ protected function getSerializableSubDataObjects()

	protected function getSerializableSubDataObjects()
	{
		return array_merge(
			parent::getSerializableSubDataObjects(),
			array('questions'));
	}

	// }}}

	// saver methods
	// {{{ protected function saveQuestions()

	protected function saveQuestions()
	{
		foreach ($this->questions as $question) {
			$question->inquisition = $this;
		}

		$this->questions->setDatabase($this->db);
		$this->questions->save();
	}

	// }}}

	// loader methods
	// {{{ protected function loadQuestions()

	protected function loadQuestions()
	{
		$sql = sprintf('select * from InquisitionQuestion
			where inquisition = %s
			order by displayorder, id',
			$this->db->quote($this->id, 'integer'));

		return SwatDB::query($this->db, $sql, 'InquisitionQuestionWrapper');
	}

	// }}}
	// {{{ protected function loadResponses()

	protected function loadResponses()
	{
		$sql = sprintf('select * from InquisitionResponse
			where inquisition = %s
			order by createdate, id',
			$this->db->quote($this->id, 'integer'));

		return SwatDB::query($this->db, $sql, 'InquisitionResponseWrapper');
	}

	// }}}
}

?>
