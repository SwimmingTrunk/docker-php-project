# Notes

### 1. Docker initially could not start because virtualization support was not detected

When I first installed Docker Desktop, it showed an error saying that virtualization support was not detected.

I checked Windows Task Manager and saw that virtualization was already enabled.

I then checked WSL from PowerShell with:

wsl --status

WSL was not installed.

I fixed this by installing WSL with:

wsl --install

After restarting Windows, Docker Desktop was able to start normally.

### 2. PowerShell did not recognize `touch`

I initially tried:

touch docker-compose.yml

PowerShell returned an error because `touch` is a Unix/Linux command and is not normally available as a PowerShell command.

I learned that PowerShell can use commands such as:

New-Item docker-compose.yml

I also practiced basic terminal commands such as:

cd
cd ..
pwd
ls
mkdir

---

## Docker Compose

### 3. The PHP container initially used the wrong Docker image

I initially used:

image: php:8.2

and tried to start Apache manually with:

apache2-foreground

The container stopped with:

exec: apache2-foreground: not found

The problem was that the normal `php:8.2` image does not contain the Apache setup I was expecting.

I fixed this by using:

image: php:8.2-apache

and removing the unnecessary command.

After this, the PHP/Apache container started correctly.

### 4. The PHP container previously stopped immediately

At one point the PHP container showed as stopped instead of running.

Using:

docker compose ps

did not show the stopped container because that command mainly shows running containers.

I learned that:

docker compose ps -a

can also show stopped containers.

This helped me find the PHP container and investigate why it had stopped.

### 5. There was a Docker Compose YAML indentation problem

At one point Docker Compose reported:

additional properties 'db' not allowed

The problem was the indentation in `docker-compose.yml`.

The `php`, `db`, and `phpmyadmin` services all needed to be directly underneath:

services:

After fixing the indentation, Docker Compose accepted the file.

### 6. PHP initially did not have the `mysqli` extension

After the Docker environment was working, PHP needed to connect to MySQL using `mysqli`.

The original:

image: php:8.2-apache

did not provide the required `mysqli` extension for the assignment.

I created a `Dockerfile`:

FROM php:8.2-apache

RUN docker-php-ext-install mysqli

I then changed the PHP service in Docker Compose from using `image:` to:

build: .

and rebuilt the containers with:

docker compose down
docker compose up -d --build

This allowed PHP to use `mysqli`.

### 7. PHP could not initially find the database table

When I first connected PHP to MySQL, I received:

Fatal error: Uncaught mysqli_sql_exception:
Table 'testdb.users' doesn't exist

The PHP-to-MySQL connection itself was working. The problem was that the `users` table did not exist in the `testdb` database.

I recreated the table in phpMyAdmin with:

* `id` — INT, Primary Key, Auto Increment
* `name` — VARCHAR(100)

I then added a test row.

After that, PHP successfully retrieved and displayed the row.

### 8. I learned that containers use service names to communicate

At first, it could have been tempting to use `localhost` when PHP connects to MySQL.

However, PHP and MySQL are running in separate containers.

The correct MySQL host is:

db

because `db` is the service name in `docker-compose.yml`.

The PHP connection therefore uses:

new mysqli("db", "user", "password", "testdb");

---

## Git

### 9. I accidentally initialized Git in the wrong directory

I was supposed to initialize Git inside:

C:\Users\nikar\OneDrive\Documents\Work\docker-php-project

but I accidentally ran:

git init

while I was in:

C:\Users\nikar

This caused Git to treat my entire Windows user folder as a Git repository.

`git status` then showed many unrelated folders and files such as:

Documents/
Downloads/
AppData/
.vscode/

and also produced permission warnings for Windows system folders.

I fixed this by removing the accidental `.git` directory from `C:\Users\nikar`.

I then navigated to the actual project directory and ran `git init` there.

### 10. I learned to check the current directory before using Git

The accidental Git repository taught me to check where I am with:

pwd

before running Git commands.

The correct project directory was:

C:\Users\nikar\OneDrive\Documents\Work\docker-php-project

After initializing Git there, `git status` correctly showed only:

Dockerfile
docker-compose.yml
src/index.php

### 11. The GitHub Pull Request initially had nothing to compare

I created a feature branch:

git checkout -b feature/docker-project

but the branch initially contained exactly the same commits as the base branch.

GitHub therefore showed:

There isn't anything to compare

A Pull Request needs a difference between the two branches.

I fixed this by making another change to `src/index.php`, committing it:

git commit -m "Improve database output"

and pushing the branch:

git push

After that, GitHub detected the difference and allowed me to create the Pull Request.

## Things I learned

The main things I learned from troubleshooting were:

* Docker Desktop on Windows depends on Windows virtualization/WSL functionality.
* PowerShell and Linux/macOS terminals do not always use the same commands.
* Docker Compose YAML indentation matters.
* `docker compose ps` and `docker compose ps -a` can show different things.
* A Docker container needs the correct image and configuration for the software it is supposed to run.
* PHP extensions can be installed when building a custom Docker image with a Dockerfile.
* Containers can communicate using Docker Compose service names.
* A Git repository should be initialized inside the project directory, not the entire Windows user directory.
* `git status` is useful for checking exactly what Git is tracking before committing.
* A Pull Request requires a difference between the source and base branches.
* GitHub checks for merge conflicts before allowing a Pull Request to be merged.
