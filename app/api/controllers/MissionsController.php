<?php
class MissionsController extends ApiController
{

    private $repo;
    private $pdo;
    private $table = "missions";

    function __construct()
    {
        $this->authorize = false;
        parent::__construct();
        $this->repo = new AppDbContext();
        $this->pdo = $this->repo->getPdo();
    }

    public function get()
    {
        $missions = $this->repo->GetAll("SELECT id AS Id, name AS Name, deadline AS Deadline, description as Description,totalsum AS TotalSum
                                         FROM $this->table");
        Response::Ok($missions);
    }

    public function find($id)
    {
        $mission = $this->repo->Find($this->table, $id);
        if (!$this->repo->isNull($mission)) {
            return Response::Ok($mission);
        }
        Response::NotFound("Mission not found");
    }

    public function create()
    {
        $model = new MissionModel();
        if ($this->repo->hasDublicate($this->table, 'name', $model->Name))
            Response::BadRequest("$model->Name alredy exists");
        $mission = R::dispense($this->table);
        $mission->name = $model->Name;
        $mission->added = date('Y-m-d H:i:s');
        $mission->deadline = $model->Deadline;
        $mission->description = $model->Description;
        if (isset($model['TotalSum'])) {
            $mission->totalsum = floatval($model['TotalSum']);
        }
        $id = R::store($mission);
        if ($id > 0) {
            $returnMission = array();
            $returnMission['Id'] = $id;
            $returnMission['Name'] = $model->Name;
            $returnMission['TotalSum'] = floatval($model['TotalSum']);
            $returnMission['Progress'] = 0;
            Response::Ok($returnMission);
        }
        Response::BadRequest("Error saving mission");
    }

    public function update()
    {
        $model = new MissionModel();
        if ($this->repo->hasDublicate($this->table, 'id', $model->Id)) {
            $mission = $this->repo->Find($this->table, $model->Id);
            $mission->name = $model->Name;
            $mission->deadline = $model->Deadline;
            $mission->description = $model->Description;
            $mission->totalsum = floatval($model->TotalSum);
            if ($this->repo->Saved($mission)) {
                Response::Ok("$model->Name updated!");
            }
            Response::BadRequest("Error updating mission");
        }
        Response::NotFound("Mission not found");
    }

    public function delete($id)
    {
        if ($this->repo->hasDublicate($this->table, 'id', $id)) {
            $mission = $this->repo->Find($this->table, $id);
            try {
                R::trash($mission);
                Response::Ok("Mission deleted successfully");
            } catch (Exception $ex) {
                Response::InternalServerError($ex->getMessage());
            }
        }
        Response::NotFound("Mission not found");
    }

    public function getforlist()
    {
        $missions = array();
        $stmt = $this->pdo->query(
            "SELECT 
            missions.id AS Id, 
            missions.name AS Name,
            totalsum AS TotalSum ,
            (SELECT COALESCE(SUM(transactions.amount),0) FROM `transactions` WHERE transactions.mission_id = missions.id) AS TotalTransactions,
            (SELECT COUNT(tasks.id) FROM `tasks` WHERE tasks.mission_id = missions.id AND tasks.done = 1) AS CompletedTasks,
            (SELECT COUNT(tasks.id) FROM `tasks` WHERE tasks.mission_id = missions.id) AS TotalTasks
        FROM `missions`
        LEFT JOIN tasks ON tasks.mission_id = missions.id
        LEFT JOIN transactions ON transactions.mission_id = missions.id
        GROUP BY missions.id
        ;"
        );
        while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
            $mission = array();

            $mission['Id'] = $row['Id'];
            $mission['Name'] = $row['Name'];
            $mission['TotalSum'] = floatval($row['TotalSum']);

            $totalSum = floatval($row['TotalSum']);
            $totalTasks = floatval($row['TotalTasks']);
            $completedTasks = floatval($row['CompletedTasks']);

            if ($totalSum > 0) {
                $transactionsSum = floatval($row['TotalTransactions']);
                $part1 = $transactionsSum + ($completedTasks * $totalSum);
                $part2 = ($totalTasks * $totalSum +  $totalSum);
                $mission['Progress'] = round(($part1 / $part2) * 100, 2);
            } else {
                $mission['Progress'] = $totalTasks != 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
            }

            array_push($missions, $mission);
        }
        Response::Ok($missions);
    }
}
