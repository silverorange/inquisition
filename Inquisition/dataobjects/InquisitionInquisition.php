<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Site/dataobjects/SiteAccount.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionResponseWrapper.php';
require_once 'Inquisition/dataobjects/'.
	'InquisitionInquisitionQuestionBindingWrapper.php';

/**
 * An inquisition
 *
 * @package   Inquisition
 * @copyright 2011-2013 silverorange
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
			array('question_bindings')
		);
	}

	// }}}

	// saver methods
	// {{{ protected function saveQuestionBindings()

	protected function saveQuestionBindings()
	{
		foreach ($this->question_bindings as $question_binding) {
			$question_binding->inquisition = $this;
		}

		$this->question_bindings->setDatabase($this->db);
		$this->question_bindings->save();
	}

	// }}}

	// loader methods
	// {{{ protected function loadResponses()

	protected function loadResponses()
	{
		$sql = sprintf(
			'select * from InquisitionResponse
			where inquisition = %s
			order by createdate, id',
			$this->db->quote($this->id, 'integer')
		);

		return SwatDB::query(
			$this->db,
			$sql,
			SwatDBClassMap::get('InquisitionResponseWrapper')
		);
	}

	// }}}
	// {{{ protected function loadQuestionBindings()

	protected function loadQuestionBindings()
	{
		$sql = sprintf(
			'select InquisitionInquisitionQuestionBinding.*
			from InquisitionInquisitionQuestionBinding
			inner join InquisitionQuestion
				on InquisitionInquisitionQuestionBinding.question =
					InquisitionQuestion.id
			where InquisitionInquisitionQuestionBinding.inquisition = %s and
				InquisitionQuestion.enabled = %s
			order by InquisitionInquisitionQuestionBinding.displayorder,
				InquisitionInquisitionQuestionBinding.id',
			$this->db->quote($this->id, 'integer'),
			$this->db->quote(true, 'boolean')
		);

		return SwatDB::query(
			$this->db,
			$sql,
			SwatDBClassMap::get('InquisitionInquisitionQuestionBindingWrapper')
		);
	}

	// }}}
}

?>
