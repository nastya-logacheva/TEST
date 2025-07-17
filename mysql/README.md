### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №28 — Репликация MySQL**

**Цель:**  
Научиться настраивать репликацию в MySQL с использованием GTID.

---

#### **Задание:**

1. Поднять стенд с двумя ВМ — `master` и `slave`;
2. Развернуть базу `bet` на мастере (дамп `bet.dmp`);
3. Настроить GTID-репликацию;
4. Реплицировать только таблицы:
   - `bookmaker`
   - `competition`
   - `market`
   - `odds`
   - `outcome`

---

#### **Ход выполнения**

1. **Подготовка стенда:**
   - Использован Vagrant + Ansible;
   - Образы: Percona Server 5.7;
   - ВМ:
     - `master` — IP `192.168.10.10`;
     - `slave` — IP `192.168.10.11`.

2. **Настройка MySQL на master:**
   - В `my.cnf`:
     ```
     server-id=1
     log_bin=mysql-bin
     gtid-mode=ON
     enforce-gtid-consistency=TRUE
     binlog-do-db=bet
     replicate-do-table=bet.bookmaker
     replicate-do-table=bet.competition
     replicate-do-table=bet.market
     replicate-do-table=bet.odds
     replicate-do-table=bet.outcome
     ```
   - Создан пользователь репликации:
     ```
     CREATE USER 'repl'@'%' IDENTIFIED BY 'replpass';
     GRANT REPLICATION SLAVE ON *.* TO 'repl'@'%';
     FLUSH PRIVILEGES;
     ```

3. **Импорт дампа базы `bet`:**
   ```
   mysql -u root -p < /vagrant/bet.dmp
   ```

4. **Настройка MySQL на slave:**
   - В `my.cnf`:
     ```
     server-id=2
     log_bin=mysql-bin
     gtid-mode=ON
     enforce-gtid-consistency=TRUE
     ```
   - Запуск репликации:
     ```
     CHANGE MASTER TO MASTER_HOST='192.168.10.10',
       MASTER_USER='repl',
       MASTER_PASSWORD='replpass',
       MASTER_AUTO_POSITION=1;
     START SLAVE;
     ```

5. **Проверка репликации:**
   ```
   SHOW SLAVE STATUS\G
   ```
   - `Slave_IO_Running: Yes`
   - `Slave_SQL_Running: Yes`
   - `Retrieved_Gtid_Set` совпадает

6. **Проверка таблиц на слейве:**
   ```
   SELECT COUNT(*) FROM bet.bookmaker;
   SELECT COUNT(*) FROM bet.odds;
   ```

---

#### **Вывод**

В результате:
- развернут master-slave стенд с MySQL 5.7;
- настроена GTID-репликация;
- реплицируются только необходимые таблицы;
- выполнена проверка через `SHOW SLAVE STATUS` и SQL-запросы.
