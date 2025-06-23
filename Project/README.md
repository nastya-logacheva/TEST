#  Проект: Форма обратной связи с полной автоматизацией

##  Описание

Проект представляет собой веб-приложение с формой обратной связи, которая отправляет сообщения по email и сохраняет их в базу данных PostgreSQL. Вся инфраструктура разворачивается автоматически с помощью **Ansible** и **Vagrant**.

Проект включает:
- Веб-интерфейс с HTTPS
- Отправку почты через msmtp (SMTP)
- Сохранение сообщений в PostgreSQL
- Репликацию master-slave
- Мониторинг с Prometheus и Alertmanager
- Централизованный сбор логов через rsyslog
- Резервное копирование и восстановление узлов

---

##  Инфраструктура

| Хост    | Назначение       | IP               |
|---------|------------------|------------------|
| web1    | Веб-сервер       | 192.168.56.10    |
| mail1   | Почтовый шлюз    | 192.168.56.11    |
| mon1    | Мониторинг       | 192.168.56.12    |
| db1     | PostgreSQL master| 192.168.56.13    |
| db2     | PostgreSQL slave | 192.168.56.14    |

---

##  Развёртывание

1. **Клонирование репозитория**
```bash
git clone ...
cd Project
```

2. **Запуск виртуальных машин**
```bash
vagrant up
```

3. **Примените Ansible-плейбуки**
   # прежде, чем применить плейбуки, требуется, настроить файл msmtprc.j2 в /templates

```bash
cd ansible
ansible-playbook -i hosts app.yaml
ansible-playbook -i hosts mail.yaml
ansible-playbook -i hosts monitoring.yaml
ansible-playbook -i hosts postgresql.yaml
ansible-playbook -i hosts backup.yaml
ansible-playbook -i hosts site.yaml


```

---

##  HTTPS

Сертификаты создаются автоматически. Форма доступна по адресу:
```
https://192.168.56.10/feedback.php
```

---

##  Основная инфраструктура в DMZ зоне
-Реализована DMZ зона - изолированный сегмент сети.
-Для каждой ВМ открыты только необходимые порты.
-Доступ пользователя только на сервисы, которые предполагаются для использования клиентом. 
-Проверка: 
```bash
vagrant@mail1:~$ sudo ufw status
```


---

##  Проверка отправки сообщений на почту + БД

- Сообщение отправляется на email через SMTP
- Данные сохраняются в таблицу `feedback` в базе данных PostgreSQL
- На ВМ db1(master) создается база feedback и таблица с правами на репликацию
- На ВМ db2(slave) выполняется pg_basebackup для создания копий данных 
- Проверка:
```sql
db1
sudo -u postgres psql -d feedback -c "SELECT * FROM feedback;"
db2
sudo -u postgres psql
\c feedback
SELECT * FROM feedback;
```


---

##  Мониторинг + Алертинг

- Prometheus доступен: http://192.168.56.12:9090
- Alertmanager: http://192.168.56.12:9093
- Метрики собираются со всех хостов через node_exporter
- Проверка алерта
```bash
vagrant@mail1:~$ sudo systemctl stop node_exporter
wait 1 min
vagrant@mail1:~$ sudo systemctl start node_exporter
```
---

##  Логирование

- Все клиенты отправляют логи на `mon1`
- Просмотр логов:  
```bash
mon1: sudo cat /var/log/client_logs.log
other vm: logger "TEST"
```

---

##  Резервное копирование

- Ежедневно по cron создаётся архив `/var/backups/postgresql_backup.tar.gz`
- Содержимое: /var/lib/postgresql
- Запуск вручную: /usr/local/bin/pg_backup.sh 
- Проверка всех копий: ls /var/backups/pgsql

---

##  Восстановление

Для восстановления сбойного узла выполните:
```bash
vagrant destroy db2
vagrant up db2
ansible-playbook -i hosts postgresql.yaml
ansible-playbook -i hosts backup.yaml
```

---

##  Автор

**Анастасия Логачева** 
