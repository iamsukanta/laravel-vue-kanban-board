<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCardRequest;
use App\Http\Requests\CreateColumnRequest;
use App\Http\Resources\CardResource;
use App\Http\Resources\ColumnListResource;
use App\Http\Resources\ColumnResource;
use App\Models\Card;
use App\Models\Column;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\DbDumper\Databases\MySql;

//use Spatie\DbDumper\Databases\MySql;

class ColumnsController extends Controller
{
    /**
     * @var Column
     */
    private $model;

    /**
     * @var Card
     */
    private $cardModel;

    /**
     * @param Column $column
     * @param Card $card
     */
    public function __construct(Column $column, Card $card)
    {
        $this->model = $column;
        $this->cardModel = $card;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \ErrorException
     */
    public function index(Request $request)
    {
        try {
            $columns = $this->model->newQuery()
                ->when($request->date, function ($q) use ($request) {
                    $q->whereDate('created_at', $request->date);
                })
                ->when($request->has('status') && $request->status == 1, function ($q) use ($request) {
                    $q->whereNull('deleted_at');
                })
                ->when($request->has('status') && !is_null($request->status) && $request->status == 0, function ($q) use ($request) {
                    $q->whereNotNull('deleted_at');
                })
                ->orderBy('id')
                ->get();

            return ColumnListResource::collection($columns);
        } catch (\ErrorException $exception) {
            throw new \ErrorException($exception->getMessage(), 400);
        }
    }

    /**
     * @param CreateColumnRequest $request
     * @return ColumnResource
     * @throws \ErrorException
     */
    public function store(CreateColumnRequest $request)
    {
        try {
            $this->model->title = $request->title;
            $this->model->save();
            return new ColumnResource($this->model);
        } catch (\ErrorException $exception) {
            throw new \ErrorException($exception->getMessage(), 400);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \ErrorException
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $column =$this->model->findOrFail($id);
            $column->deleted_at = date('Y-m-d H:i:s');
            $column->save();

            $this->cardModel->where('column_id', $column->id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
            DB::commit();
            return response(['message'=> 'Column and related cards Successfully Deleted'], 200);
        } catch (\ErrorException $exception) {
            DB::rollBack();
            throw new \ErrorException($exception->getMessage(), 400);
        }
    }

    /**
     * @param $id
     * @param CreateCardRequest $request
     * @return CardResource
     * @throws \ErrorException
     */
    public function storeCard($id, CreateCardRequest $request)
    {
        try {
            $cardCount = $this->cardModel->where('column_id', $id)->count();
            $this->cardModel->position = $cardCount;
            $this->cardModel->column_id = $id;
            $this->cardModel->title = $request->title;
            $this->cardModel->description = $request->description;
            $this->cardModel->save();
            return new CardResource($this->cardModel);
        } catch (\ErrorException $exception) {
            throw new \ErrorException($exception->getMessage(), 400);
        }
    }

    /**
     * @param $id
     * @param $cardId
     * @param CreateCardRequest $request
     * @return CardResource
     * @throws \ErrorException
     */
    public function updateCard($id, $cardId, CreateCardRequest $request)
    {
        try {
            $this->cardModel = $this->cardModel->findOrFail($cardId);
            $this->cardModel->title = $request->title;
            $this->cardModel->description = $request->description;
            $this->cardModel->save();
            return new CardResource($this->cardModel);
        } catch (ModelNotFoundException $exception) {
            throw new \ErrorException($exception->getMessage(), 400);
        } catch (\ErrorException $exception) {
            throw new \ErrorException($exception->getMessage(), 400);
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \ErrorException
     */
    public function getCards($id, Request $request)
    {
        try {
            $cards = $this->cardModel->newQuery()
                ->when($request->date, function ($q) use ($request) {
                    $q->whereDate('created_at', $request->date);
                })
                ->when($request->has('status') && $request->status == 1, function ($q) use ($request) {
                    $q->whereNull('deleted_at');
                })
                ->when($request->has('status') && !is_null($request->status) && $request->status == 0, function ($q) use ($request) {
                    $q->whereNotNull('deleted_at');
                })
                ->orderBy('id')
                ->get();
            return CardResource::collection($cards);
        } catch (\ErrorException $exception) {
            throw new \ErrorException($exception->getMessage(), 400);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \ErrorException
     */
    public function exportDB()
    {
        try {
            MySql::create()
                ->setDbName(config('database.connections.mysql.database'))
                ->setUserName(config('database.connections.mysql.username'))
                ->setPassword(config('database.connections.mysql.password'))
                ->dumpToFile('dump.sql');

            return response()->download(public_path().'/dump.sql')->deleteFileAfterSend(true);
        } catch (\ErrorException $exception) {
            throw new \ErrorException($exception->getMessage(), 400);
        }
    }



}
