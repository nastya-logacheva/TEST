### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №29 — Репликация PostgreSQL**

**Цель:**  
Научиться настраивать hot_standby-репликацию PostgreSQL с использованием слотов и организовывать корректное резервное копирование.

---

#### **Задание:**

1. Настроить hot_standby-репликацию PostgreSQL с использованием слотов;
2. Настроить резервное копирование с возможностью восстановления.

---

#### **Ход выполнения**

1. **Подготовка стенда:**
   - Использован Vagrant + Ansible;
   - ВМ:
     - `node1` — master PostgreSQL;
     - `node2` — реплика;
   - Версия PostgreSQL: 14.18 на Ubuntu 22.04.

2. **Настройка master-сервера (node1):**
   - В `postgresql.conf`:
     ```
     wal_level = replica
     max_wal_senders = 3
     wal_keep_size = 64
     hot_standby = on
     max_replication_slots = 1
     ```
   - В `pg_hba.conf`:
     ```
     host replication replicator 192.168.10.0/24 md5
     ```
   - Создан пользователь:
     ```
     CREATE ROLE replicator REPLICATION LOGIN ENCRYPTED PASSWORD 'replpass';
     ```
   - Создан слот:
     ```
     SELECT * FROM pg_create_physical_replication_slot('otus_slot');
     ```

3. **Настройка replica-сервера (node2):**
   - Бэкап с `pg_basebackup`:
     ```
     pg_basebackup -h 192.168.10.10 -D /var/lib/postgresql/14/main        -U replicator -Fp -Xs -P -R --slot=otus_slot
     ```
   - Права и владелец каталога восстановлены;
   - Файл `standby.signal` создаётся автоматически;
   - В `postgresql.conf` указано:
     ```
     primary_conninfo = '... user=replicator ...'
     ```

4. **Проверка репликации:**
   - На master:
     ```
     SELECT * FROM pg_stat_replication;
     ```
   - На реплике:
     ```
     SELECT pg_is_in_recovery();  -- true
     ```
   - Создана БД на master:
     ```
     CREATE DATABASE otus_test;
     ```
   - Проверено наличие на replica:
     ```
     \l
     ```

5. **Резервное копирование:**
   - Использован `pg_basebackup`:
     ```
     pg_basebackup -D /backup/pg -Ft -z -P -U replicator
     ```
   - Проверка успешного создания архива `.tar.gz`.

---

#### **Вывод**

В результате:
- реализована hot_standby-репликация PostgreSQL через слоты;
- использован `pg_basebackup` для копирования и восстановления;
- проверено с помощью `pg_stat_replication` и `pg_is_in_recovery`;
