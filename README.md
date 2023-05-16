# Загрузка данных из json-файла. 
Построение дерева из загруженного массива.
Формат:
```
[
{ "id": 1, "name": "Node 1", "parent_id": null },
{ "id": 2, "name": "Node 2", "parent_id": 1 },
{ "id": 3, "name": "Node 3", "parent_id": 1 },
{ "id": 4, "name": "Node 4", "parent_id": 2 },
]
```

# Реализован класс Tree. Получает данные из массива. Для хранения и экономии памяти используются временные файлы.
Реализован поиск узла, поиск поддерева по узлу, обход дерева
Есть юнит-тесты, в том числе и на большие файлы. 
Вместо работы с массивами используются генераторы. 
Была задумка также реализовать дополнительные провайдеры для БД и Redis, но не хватило времени.
Для оптимизации скорости поиска можно поиграться с количеством страниц на котрые разбивается исходный массив.

# Docker

Для задачи сделано окружение в docker, все команды запускаются в докере.
Для развертывания запустить 
```
docker compose up
```
из корневой папки

# Работа с тестовыми данными
```
root@1d79d5e0e2e8:/app# php console.php tree:json-load -h    
Usage:
  tree:json-load [options] [--] <json>

Arguments:
  json                   Path to JSON file

Options:
  -m, --memory[=MEMORY]  Memory limit for tree building
  -i, --in-memory        Save tree to memory
  -b, --db               Save tree to DB
  -r, --redis            Save tree to Redis
  -f, --file             Save tree to filesystem
  -l, --list             List all nodes
  -t, --traverse         Traverse tree
  -s, --search[=SEARCH]  Search node(s) (multiple values allowed)
  -p, --path[=PATH]      Show node(s) path (multiple values allowed)
```
Пример работы
```
php console.php tree:json-load /app/data/data.json -f -l -s 5 -s 6 -s 12 -p 6 -p 10 -p 1 -t
```
Результат
```
List nodes
ID: 1, ParentID: , name: Node 1
ID: 2, ParentID: 1, name: Node 2
ID: 4, ParentID: 2, name: Node 4
ID: 6, ParentID: 4, name: Node 6
ID: 7, ParentID: 4, name: Node 7
ID: 10, ParentID: 7, name: Node 10
ID: 5, ParentID: 2, name: Node 5
ID: 3, ParentID: 1, name: Node 3
ID: 8, ParentID: 3, name: Node 8
ID: 9, ParentID: 3, name: Node 9

Search nodes
Node: 5
ID: 5, ParentID: 2, name: Node 5 
Node: 6
ID: 6, ParentID: 4, name: Node 6 
Node: 12
Not found

Path to nodes
Node: 6
ID: 6, ParentID: 4, name: Node 6 
ID: 4, ParentID: 2, name: Node 4 
ID: 2, ParentID: 1, name: Node 2 
ID: 1, ParentID: null, name: Node 1 

Node: 10
ID: 10, ParentID: 7, name: Node 10 
ID: 7, ParentID: 4, name: Node 7 
ID: 4, ParentID: 2, name: Node 4 
ID: 2, ParentID: 1, name: Node 2 
ID: 1, ParentID: null, name: Node 1 

Node: 1
ID: 1, ParentID: null, name: Node 1 


Traverse
I'm Node 1(1)
I'm Node 2(2)
I'm Node 4(4)
I'm Node 6(6)
I'm Node 7(7)
I'm Node 10(10)
I'm Node 5(5)
I'm Node 3(3)
I'm Node 8(8)
I'm Node 9(9)
```

# Генерация файла с данными

```
php console.php tree:file-generate -h

Usage:
tree:file-generate [options] [--] [<filename> [<dir>]]

Arguments:
filename                       Directory to store generated files [default: false]
dir                            Directory to store generated files [default: false]

Options:
-r, --rows[=ROWS]              Set rows count [default: 100000]
-b, --batch[=BATCH]            Set batch size to adjust memory/performance [default: 10000]
-s, --start-node[=START-NODE]  Node start ID [default: 1]
-t, --step[=STEP]              Step between node IDs [default: 1]
-l, --label[=LABEL]            Node name prefix [default: "Node"]
-g, --generate-name            Generate name instead of default
-x, --recreate                 Recreate file if exists
-m, --memory[=MEMORY]          Memory limit for tree building
```
Пример
```
php console.php tree:file-generate -r 1234567 -b 23400 -s 33 -t 12 -g -x            
```
Результат
```
Generated file: /app/data/fake_data_1_234_567.json
Time: 7.63 s
Memory: 4 Mb
```
