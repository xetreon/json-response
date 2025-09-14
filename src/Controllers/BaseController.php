<?php

namespace Xetreon\JsonResponse\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Xetreon\JsonResponse\Traits\ResponseTrait;
use Xetreon\JsonResponse\Traits\LoggerTrait;
use Xetreon\JsonResponse\Traits\ValidatorTrait;

/**
 * BaseController
 *
 * Provides:
 * - Consistent API responses
 * - Validation helpers
 * - Pagination and query helpers
 * - Automatic logging
 */
class BaseController extends Controller
{
    use ResponseTrait, LoggerTrait, ValidatorTrait;

    /**
     * Default pagination count.
     */
    protected int $paginateCount = 15;

    /**
     * Set pagination size from request, fallback to default.
     */
    protected function setPaginateSize(Request $request): int
    {
        $pageSize = $request->input('per_page', $this->paginateCount);

        if (is_numeric($pageSize)) {
            $this->paginateCount = (int) $pageSize;
        }

        return $this->paginateCount;
    }

    /**
     * Fetch data with optional filtering, sorting, and pagination.
     *
     * @param Builder|Model $query
     * @param array $inputs
     */
    protected function fetchFromQuery(Builder|Model $query, array $inputs): JsonResponse
    {
        $sortBy = $inputs['sort_by'] ?? 'id';
        $sortOrder = $inputs['sort_order'] ?? 'DESC';

        if (!empty($inputs['status']) && is_array($inputs['status'])) {
            $query->whereIn('status', $inputs['status']);
        }

        $query->orderBy($sortBy, $sortOrder);
        $paginatedData = $query->paginate($inputs['per_page'] ?? $this->paginateCount);

        return $this->success(true, $paginatedData, 'Data fetched successfully', Response::HTTP_OK);
    }

    /**
     * Fetch a single record by ID.
     */
    protected function fetchById(Builder|Model $query, int $id, string $notFoundMessage = 'Record not found'): JsonResponse
    {
        $record = $query->find($id);

        if (!$record) {
            return $this->error(false, [], $notFoundMessage, Response::HTTP_NOT_FOUND);
        }

        return $this->success(true, $record, 'Record fetched successfully', Response::HTTP_OK);
    }
}
