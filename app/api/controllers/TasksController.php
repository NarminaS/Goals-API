<?php

class TasksController extends ApiController
{

    private $repo;
    private $pdo;
    private $table = "tasks";

    function __construct()
    {
        $this->authorize = false;
        parent::__construct();
        $this->repo = new AppDbContext();
        $this->pdo = $this->repo->getPdo();
    }

    public function getall($missionId)
    {
        $tasks = R::getAll("SELECT id AS Id, mission_id AS MissionId, text AS Text, totalsum AS TotalSum, done AS Done
                                      FROM $this->table WHERE mission_id = ?", array($missionId));
        Response::Ok($tasks);
    }

    public function create()
    {
        $model = new TaskModel();
        if ($this->repo->hasDublicate($this->table, 'text', $model->Text))
            Response::BadRequest("$model->Text alredy exists");
        $task = R::dispense($this->table);
        $task->mission_id = $model->MissionId;
        $task->text = $model->Text;
        if (isset($model['TotalSum'])) {
            $task->totalsum = floatval($model['TotalSum']);
        }
        $task->done = $model->Done;
        if ($this->repo->Saved($task)) {
            Response::Ok("$model->Text saved!");
        }
        Response::BadRequest("Error saving vendor");
    }
}
