# Distributed Systems Project App

## Requirements

- The only requirement is to have Docker installed on your machine.

## How to run

- Clone the repository

- Run the following command to build the Docker image:

```bash
./setup.sh
```

- Run the migrations to set up the database:

```bash
./vendor/bin/sail artisan migrate
```

## How to use

Run the application:

```bash
./vendor/bin/sail up -d
```

Stop the application:

```bash
./vendor/bin/sail down
```

