# Popcorn Time Ru API server

It is an API server, you don't need this to watch films on the client.<br>
Just download the client from project page (https://github.com/popcorn-official/popcorn-desktop).

If you want to add some trackers or create a self-hosted server then you can fork it.

It is hosted in free tier google cloud - search is slow - no elastic and 580 mb ram.

No anime for at the moment, please extend the api if you know an api for anime like TMDB for films and shows.

## Deployment
It is a standard symfony v5 application that requires:<br>
`nginx, php-8.1, mariadb, cron, tor, elasticsearch, git, redis`<br>

It is highly recommended that you configure a nginx cache.

### Docker Desktop local stack

This repository includes a Docker Compose stack for local development. It runs the services listed above with project-specific service names, volumes, network, and high host ports so it can run beside other projects such as Laravel Sail.

Default local ports:

- API: `http://127.0.0.1:18080`
- Adminer: `http://127.0.0.1:18081`
- MariaDB: `127.0.0.1:13306`
- Redis: `127.0.0.1:16379`
- Elasticsearch: `http://127.0.0.1:19200`

Start the stack:

```sh
docker compose up --build
```

The first run installs Composer dependencies into the `popcorn-server-vendor` Docker volume and runs a one-shot setup container that creates the local database schema and enqueue tables. If you need TMDB or Trakt-backed spider data, pass local environment values without committing them:

```sh
TMDB_API_KEY=your-tmdb-v3-key TRAKT_KEY=your-trakt-client-id docker compose up --build
```

Useful local commands:

```sh
docker compose exec popcorn-server-app php bin/console cache:clear
docker compose exec popcorn-server-app php bin/console spider:run --all
docker compose exec popcorn-server-app php bin/console fos:elastica:populate
docker compose logs -f popcorn-server-worker
```

### Installation guide for RHEL 8/9 based systems
You can find the installation guide for RHEL 8/9 based systems [here](Documentation/RHEL-8-9.md).

### Installation guide for Debian 10/11 based systems
You can find the installation guide for Debian 10/11 based systems [here](Documentation/Debian-10-11.md).

### Configuring Search
You can configure search in `config/services.yaml`.

### Issues with spiders & tor
If you have issues with some spiders, setup tor node and configure tor proxy.<br>
Refer to the respective installation guides above for tor<br>

### Initialise the database
You can run the following command to initialise the database.<br>
```sh
bin/console spider:run --all
```

### Grafana dashboard
Additionally, you may set up grafana and use `grafana.json` for the app dashboard.<br>

### Ansible install
The deployment playbook for a single server is located in the deploy folder.

### Debug Console
You can run the following command to view a live debug log of the API.<br>
```
bin/console enqueue:consume -vvv --logger=stdout
```
