<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
    /**
     * Проверка получения списка задач.
     * Сортировка по умолчанию: по убыванию id.
     */
    public function testList(): void
    {
        $client = static::createClient();

        $this->addTask($client, [
            'name' => 'test get list'
        ]);

        // Получение списка задач
        $client->xmlHttpRequest('GET', '/task');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $json = json_decode($content, true);

        $this->assertIsArray($json);
        $this->assertNotEmpty($json);

        $task = $json[0];
        $this->assertArrayHasKey('name', $task);
        $this->assertEquals('test get list', $task['name']);
    }

    /**
     * Проверка получения списка задач c фильтрацией.
     * Сортировка по умолчанию: по убыванию id.
     */
    public function testListWithFilter(): void
    {
        $client = static::createClient();

        $this->addTask($client, [
            'name' => 'test 1',
            'status' => 1
        ]);
        $this->addTask($client, [
            'name' => 'test 2'
        ]);

        // Получение списка задач с фильтрацией "только завершенные задачи".
        // В резульате последняя добавленная задача не попадет в список.
        $client->xmlHttpRequest('GET', '/task?status=1');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $json = json_decode($content, true);

        $task = $json[0];
        $this->assertEquals('test 1', $task['name']);
        $this->assertEquals(1, $task['status']);
    }

    /**
     * Проверка получения списка задач c сортировкой.
     */
    public function testListWithSorting(): void
    {
        $client = static::createClient();

        $this->addTask($client, [
            'name' => 'test1 sort'
        ]);
        $this->addTask($client, [
            'name' => 'test2 sort',
            'status' => 1
        ]);
        $this->addTask($client, [
            'name' => 'test3 sort'
        ]);

        // Получение списка задач c сортировкой:
        // сначала по убыванию статуса, затем по убыванию id
        $client->xmlHttpRequest('GET', '/task?sort=-status,-id');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $json = json_decode($content, true);

        $this->assertIsArray($json);
        $this->assertNotEmpty($json);

        $task = $json[0];
        $this->assertArrayHasKey('name', $task);
        $this->assertEquals('test2 sort', $task['name']);
    }

    /**
     * Проверка получения списка задач c заданными параметрами паджинации.
     * Сортировка по умолчанию: по убыванию id.
     */
    public function testListWithPagination(): void
    {
        $client = static::createClient();

        $this->addTask($client, [
            'name' => 'test1 sort'
        ]);
        $this->addTask($client, [
            'name' => 'test2 sort'
        ]);
        $this->addTask($client, [
            'name' => 'test3 sort'
        ]);
        $this->addTask($client, [
            'name' => 'test4 sort'
        ]);

        // Получение списка задач c паджинацией:
        // выведем 2 строки на 2йстранице (попадет задачи "test2 sort", "test1 sort")
        $client->Request('GET', '/task?x-pagination-size=2&x-pagination-page=2');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $json = json_decode($content, true);

        $this->assertIsArray($json);
        $this->assertNotEmpty($json);

        $task = $json[0];
        $this->assertArrayHasKey('name', $task);
        $this->assertEquals('test2 sort', $task['name']);
    }


    /**
     * Проверка добавления задачи.
     */
    public function testAddTask(): void
    {
        $client = static::createClient();
        $task = $this->addTask($client, [
            'name' => 'test task 1',
            'description' => 'task 1 for tests'
        ]);
        $this->assertArrayHasKey('id', $task);
        $this->assertArrayHasKey('name', $task);
        $this->assertArrayHasKey('description', $task);
        $this->assertEquals('test task 1', $task['name']);
        $this->assertEquals('task 1 for tests', $task['description']);
    }

    /**
     * Проверка изменения задачи.
     */
    public function testEditTask(): void
    {
        $client = static::createClient();
        $task = $this->addTask($client, [
            'name' => 'test task for edit 1',
            'description' => 'task 1 for tests'
        ]);
        $task ['name'] = 'new test task for edit 1';

        $client->jsonRequest('PUT', "/task/" . $task['id'], $task);
        $this->assertResponseIsSuccessful();

        $this->assertArrayHasKey('id', $task);
        $this->assertArrayHasKey('name', $task);
        $this->assertArrayHasKey('description', $task);
        $this->assertEquals('new test task for edit 1', $task['name']);
        $this->assertEquals('task 1 for tests', $task['description']);
    }

    /**
     * Проверка удаления задачи.
     */
    public function testDeleteTask(): void
    {
        $client = static::createClient();
        $task = $this->addTask($client, [
            'name' => 'test task 1'
        ]);

        $client->jsonRequest('DELETE', "/task/" . $task['id']);
        $this->assertResponseIsSuccessful();
    }

    /**
     * Функция добавления задачи.
     * @param KernelBrowser $client
     * @param array $data
     */
    protected function addTask(KernelBrowser $client, array $data): array
    {
        $client->jsonRequest('POST', '/task', $data);

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $task = json_decode($content, true);
        $this->assertArrayHasKey('id', $task);

        return $task;
    }
}
