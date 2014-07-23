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
 * @copyright 2011-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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

		$sql = sprintf(
			'select * from InquisitionResponse
			where account = %s and inquisition = %s',
			$this->db->quote($account->id, 'integer'),
			$this->db->quote($this->id, 'integer')
		);

		$wrapper = $this->getResolvedResponseWrapperClass();
		$response = SwatDB::query($this->db, $sql, $wrapper)->getFirst();

		if ($response instanceof InquisitionResponse) {
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
			array(
				'question_bindings',
				'visible_question_bindings',
			)
		);
	}

	// }}}
	// {{{ protected function getResolvedResponseWrapperClass()

	protected function getResolvedResponseWrapperClass()
	{
		return SwatDBClassMap::get($this->getResponseWrapperClass());
	}

	// }}}
	// {{{ protected function getResponseWrapperClass()

	protected function getResponseWrapperClass()
	{
		return 'InquisitionResponseWrapper';
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
			$this->getResolvedResponseWrapperClass()
		);
	}

	// }}}
	// {{{ protected function loadQuestionBindings()

	protected function loadQuestionBindings()
	{
		$sql = sprintf(
			'select * from InquisitionInquisitionQuestionBinding
			where inquisition = %s order by displayorder, id',
			$this->db->quote($this->id, 'integer')
		);

		return SwatDB::query(
			$this->db,
			$sql,
			SwatDBClassMap::get('InquisitionInquisitionQuestionBindingWrapper')
		);
	}

	// }}}
	// {{{ protected function loadVisibleQuestionBindings()

	protected function loadVisibleQuestionBindings()
	{
		$sql = sprintf(
			'select InquisitionInquisitionQuestionBinding.*
			from InquisitionInquisitionQuestionBinding
			inner join VisibleInquisitionQuestionView
				on InquisitionInquisitionQuestionBinding.question =
					VisibleInquisitionQuestionView.question
			where InquisitionInquisitionQuestionBinding.inquisition = %s
			order by InquisitionInquisitionQuestionBinding.displayorder,
				InquisitionInquisitionQuestionBinding.id',
			$this->db->quote($this->id, 'integer')
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
