### **OTUS — Administrator Linux. Professional.**  
#### **Домашнее задание №25 — Строим бонды и VLAN'ы**

**Цель:**  
Научиться настраивать VLAN и агрегировать каналы с помощью bonding (LACP).

---

#### **Задание:**

1. В Office1, в тестовой подсети, появляются клиенты и серверы с доп. интерфейсами:
   - testClient1 — 10.10.10.254
   - testClient2 — 10.10.10.254
   - testServer1 — 10.10.10.1
   - testServer2 — 10.10.10.1

2. Развести по VLAN:
   - testClient1 ↔ testServer1 (VLAN 100);
   - testClient2 ↔ testServer2 (VLAN 200);

3. Между `centralRouter` и `inetRouter`:
   - проброшено два линка;
   - объединены в bonding-интерфейс;
   - реализована отказоустойчивость.

---

#### **Ход выполнения**

1. **Подготовка стенда:**
   - Использован Vagrant + Ansible;
   - Подняты дополнительные машины: `testClient1`, `testClient2`, `testServer1`, `testServer2`;
   - Сетевая схема реализована в соответствии с методичкой:
     [Методичка по VLAN и bonding](https://docs.google.com/document/d/1BO5cUT0u4ABzEOjogeHyCaNiYh76Bh73/edit)

2. **Настройка VLAN:**
   - Использован `nmcli`:
     ```
     nmcli con add type vlan ifname vlan100 dev eth1 id 100 ip4 10.10.10.254/24
     nmcli con add type vlan ifname vlan200 dev eth1 id 200 ip4 10.10.10.254/24
     ```
   - На клиентах настроены VLAN-интерфейсы;
   - На серверах аналогично — с IP 10.10.10.1.

3. **Проверка VLAN-связности:**
   - ping между `testClient1` и `testServer1` — работает;
   - `testClient2` ↔ `testServer2` — работает;
   - Между VLAN — маршрутизация отсутствует.

4. **Настройка bonding между `inetRouter` и `centralRouter`:**
   - Добавлены дополнительные интерфейсы `eth1`, `eth2`;
   - Создан bonding-интерфейс:
     ```
     nmcli con add type bond ifname bond0 mode active-backup
     nmcli con add type ethernet slave-type bond con-name slave1 ifname eth1 master bond0
     nmcli con add type ethernet slave-type bond con-name slave2 ifname eth2 master bond0
     ```
   - Назначен IP:
     ```
     nmcli con mod bond0 ipv4.addresses 192.168.100.1/30
     ```

5. **Проверка отказоустойчивости:**
   - При отключении `eth1` — линк не падает, связь сохраняется;
   - Аналогично при отключении `eth2`.

---

#### **Вывод**

В результате:
- настроены два VLAN и обеспечена изоляция трафика между парами клиент-сервер;
- реализован bonding-интерфейс между маршрутизаторами;
- проверена устойчивость линка к сбоям отдельных интерфейсов;
- вся настройка выполнена вручную и через Ansible.
