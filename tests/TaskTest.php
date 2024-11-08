<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
    public function testList(): void
    {
        $client = static::createClient();

        $this->addTask($client, [
            'name' => 'test get list',
            'status' => 0
        ]);

        $client->xmlHttpRequest('GET', '/task');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $json = json_decode($content, true);

        $this->assertIsArray($json);
        $this->assertNotEmpty($json);

        $task = $json[0];
        $this->assertArrayHasKey('name', $task);
        $this->assertEquals('test get list', $task['name']);

        $client->xmlHttpRequest('GET', '/task?status=1');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $json = json_decode($content, true);

        $this->assertIsArray($json);
        if (empty($json)) {
            $this->assertTrue(true);
        } else {
            $task = $json[0];
            $this->assertArrayHasKey('name', $task);
            $this->assertNotEquals('test get list', $task['name']);
        }
    }

    public function testAddTask(): void
    {
        $client = static::createClient();
        $task = $this->addTask($client, [
            'name' => 'test task 1',
            'description' => 'task 1 for tests',
            'status' => 0
        ]);
        $this->assertArrayHasKey('id', $task);
        $this->assertArrayHasKey('name', $task);
        $this->assertArrayHasKey('description', $task);
        $this->assertEquals('test task 1', $task['name']);
        $this->assertEquals('task 1 for tests', $task['description']);
    }

    public function testDeleteTask(): void
    {
        $client = static::createClient();
        $task = $this->addTask($client, [
            'name' => 'test task 1',
            'status' => 0
        ]);
        $client->jsonRequest('DELETE', "/task/" . $task['id']);
        $this->assertResponseIsSuccessful();
    }

    protected function addTask(KernelBrowser $client, array $data): array
    {
        $client->jsonRequest('POST', '/task', $data);
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $json = json_decode($content, true);

        $task = $json;
        $this->assertArrayHasKey('id', $task);
        return $task;
    }
}
