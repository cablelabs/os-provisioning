<?php
/**
 * Service
 *
 * @author     Esben Petersen
 * @link       https://github.com/esbenp/larapi/blob/master/api/Users/Services/UserService.php
 */

namespace App\V1;

use Illuminate\Events\Dispatcher;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Service
{
    private $dispatcher;

    private $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    public function getAll($options = [])
    {
        return $this->repository->get($options);
    }

    public function getById($id, array $options = [])
    {
        $model = $this->repository->getById($id, $options);

        if (is_null($model)) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    public function create($data)
    {
        $model = $this->repository->create($data);
        //TODO: Fire event of the specific event class example: TicketWasCreated on every module modules/Ticketsystem/Events/TicketWasCreated
//        $dispatcher->dispatch(new ModelWasCreated($model));

        return $model;
    }

    public function update($id, array $data)
    {
        $model = $this->getById($id);

        $this->repository->update($model, $data);

        //TODO: Fire event of the specific event class example: TicketWasDeleted on every module modules/Ticketsystem/Events/TicketWasUpdated
//        $dispatcher->dispatch(new ModelWasUpdated($model));

        return $model;
    }

    public function delete($id)
    {
        $this->repository->delete($id);

        //TODO: Fire event of the specific event class example: TicketWasDeleted on every module modules/Ticketsystem/Events/TicketWasDeleted
//        $this->dispatcher->dispatch(new ModelWasDeleted($model));
    }
}
