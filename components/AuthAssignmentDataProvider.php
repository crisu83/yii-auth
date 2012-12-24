<?php

class AuthAssignmentDataProvider extends CDataProvider
{
	public $userId;

	private $_assignments = array();

	/**
	 * @param CAuthAssignment[] $authAssignments
	 */
	public function setAuthAssignments($authAssignments)
	{
		$data = array();
		foreach ($authAssignments as $authAssignment)
			$data[] = $authAssignment;
		$this->_assignments = $data;
	}

	/**
	 * Fetches the data from the persistent data storage.
	 * @return array list of data items
	 */
	protected function fetchData()
	{
		if (empty($this->_assignments) && $this->userId !== null)
		{
			$assignments = Yii::app()->authManager->loadAuthAssignments($this->userId);
			$this->setAuthAssignments($assignments);
		}

		return $this->_assignments;
	}

	/**
	 * Fetches the data item keys from the persistent data storage.
	 * @return array list of data item keys.
	 */
	protected function fetchKeys()
	{
		return array('itemname', 'userid', 'bizrule', 'data');
	}

	/**
	 * Calculates the total number of data items.
	 * @return integer the total number of data items.
	 */
	protected function calculateTotalItemCount()
	{
		return count($this->_assignments);
	}
}
