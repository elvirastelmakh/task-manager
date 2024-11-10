<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\HeaderBag;

class parseRequestParamsHelper
{

    /**
     * Поиск параметров паджинации
     * @param HeaderBag $headers
     * @param array $params
     * @return array Returns an array of pagination params
     */
    public static function parsePaginationParams(HeaderBag $headers, array $params): array
    {
        $paginationParams = [];
        $xPaginationSize = 0;
        $xPaginationPage = 0;
        if (
            $headers->get('x-pagination-size') &&
            $headers->get('x-pagination-page')
        ) {
            $xPaginationSize = (int)($headers->get('x-pagination-size')) ?? 20;
            $xPaginationPage = (int)($headers->get('x-pagination-page')) ?? 1;
        }
        if (
            array_key_exists('x-pagination-size', $params) &&
            array_key_exists('x-pagination-page', $params)
        ) {
            $xPaginationSize = (int)($params['x-pagination-size']) ?? 20;
            $xPaginationPage = (int)($params['x-pagination-page']) ?? 1;
        }

        if (
            $xPaginationSize > 0 &&
            $xPaginationPage > 0
        ) {
            $paginationParams['xPaginationSize'] = $xPaginationSize;
            $paginationParams['xPaginationPage'] = $xPaginationPage;
        }

        return $paginationParams;
    }

    /**
     * Поиск параметров сортировки
     * @param array $params
     * @return array Returns an array of sorting params
     */
    public static function parseSortingParams(array $params): array
    {
        if (
            !array_key_exists('sort', $params) ||
            empty($params['sort'])
        ) {
            return [];
        }

        $sortArr = explode(',', trim($params['sort']));
        if (empty($sortArr))
        {
            return [];
        }

        $sortingParams = [];
        foreach ($sortArr as $sort)
        {
            $sort = trim($sort);
            $sortType = 'ASC';
            if (substr($sort, 0, 1) == '-') {
                $sortType = 'DESC';
                $sort = substr($sort, 1);
            }
            if (substr($sort, 0, 1) == '+') {
                $sort = substr($sort, 1);
            }
            $sortingParams[$sort] = $sortType;
        }

        return $sortingParams;
    }
}