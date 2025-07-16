### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №11 — Bash**

**Цель:**  
Написать скрипт на Bash, который будет запускаться по расписанию и отправлять статистику по логам веб-сервера на email.

**Критерии приёма:**  
Скрипт должен:
1. Каждые час запускаться через cron;
2. Формировать письмо со следующей информацией:
   - IP-адреса с наибольшим количеством запросов;
   - URL с наибольшим количеством запросов;
   - ошибки веб-сервера/приложения;
   - статистика по кодам HTTP-ответов;
3. Работать с момента последнего запуска (анализировать дельту);
4. Предотвращать параллельный запуск нескольких копий.

---

#### **Ход выполнения**

##### Пример отработки:
Первый раз:
```
$ ./otus_bash.sh access-4560-644067.log 
script report
=====
Log from 2000-01-01T00:00:00 to 2025-05-02T14:45:52+00:00

5 most popular IPs
=====
     39 109.236.252.130
     36 212.57.117.19
     33 188.43.241.106
     17 217.118.66.161
     17 185.6.8.9

4 most popular requests
=====
    151 GET /
     61 POST /wp-login.php
     59 GET /wp-login.php
     57 POST /xmlrpc.php
     23 GET /robots.txt

Top server-errors-requests
=====
      1 POST /wp-content/uploads/2018/08/seo_script.php HTTP/1.1" 500
      1 GET /wp-includes/ID3/comay.php HTTP/1.1" 500
      1 GET /wp-content/plugins/uploadify/includes/check.php HTTP/1.1" 500

HTTP-codes
=====
    497 200
     95 301
     48 404
      7 400
      3 500
      2 499
      1 403
      1 304
```
Второй раз:
```
$ ./otus_bash.sh access-4560-644067.log 
script report
=====
Log from 2025-05-02T14:45:52+00:00 to 2025-05-02T14:45:56+00:00

5 most popular IPs
=====

4 most popular requests
=====

Top server-errors-requests
=====

HTTP-codes
=====
```

Добавляем в CRON:
```
0 */1 * * * /bin/bash -l -c '/home/vagrant/otus_bash.sh /path/to/access-4560-644067.log | mail -s "LOG REPORT" "email@example.com"'
```

---

#### **Вывод**

В результате:
- создан универсальный Bash-скрипт для анализа логов;
- скрипт выполняется по расписанию через cron;
- предотвращён повторный запуск;
- отчёт отправляется на email;
- все требования выполнены.

Задание оформлено и готово к проверке.
