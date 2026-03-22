# 🔮 极客高阶玩法与调优 (Performance Tuning)

> **🎯 极客战术**：任何软件的默认配置，都是向“最低端硬件”妥协的产物。默认配置只是为了“能跑”，而极客的追求是“起飞”。在这份指南中，我们将带你深入引擎底层，榨干服务器的每一滴物理性能。

**⚠️ 极客警告 (OOM 预警)**  
接下来的所有调优操作，都具有极强的侵入性。在修改内存池和并发数之前，请务必对你服务器的物理内存（RAM）和 CPU 核心数有绝对的掌控。盲目调大参数会导致系统触发 OOM (Out Of Memory) 杀手，直接引起服务器内核级宕机！

**特别警告：本操作属于系统级别，建议对系统有充分了解的玩家操作。**

---

**🚀 一、 Nginx 引擎超频**

Nginx 的抗并发能力极强，但默认的 `worker_connections` (单进程连接数) 往往被保守地设置在 768 或 1024。我们需要彻底解除这个封印。

用编辑器打开 Nginx 全局主配置文件：
```bash
nano /etc/nginx/nginx.conf
```
* **并发封印解除**  
找到 `worker_processes` 和 `events` 块，修改为极客参数：
```
# 自动匹配 CPU 物理核心数，不浪费任何一个线程
worker_processes auto;

events {
    # 开启 epoll 网络模型（Linux 环境下最高效的 I/O 模型）
    use epoll;
    # 单个 worker 的最大并发连接数提升至极限
    worker_connections 65535;
    # 允许一个 worker 同时接受多个新连接
    multi_accept on;
}
```

* **带宽压缩引擎 (Gzip)**  
在 `http { ... }` 块中，找到 `gzip` 相关代码并取消注释，这能让你的网页体积瞬间缩小 60% 以上，实现真正意义上的秒开：
```
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 5; # 压缩级别 1-9，5 是 CPU 消耗与压缩比的最佳黄金分割点
gzip_min_length 256;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
```

保存后，重载引擎：`nginx -t && nginx -s reload`

**⚙️ 二、 PHP-FPM 性能榨取**  

如果你经常在流量高峰期遇到 502 Bad Gateway，那百分之百是因为 PHP 的进程池（Pool）被塞满了。

打开 PHP-FPM 的进程池配置文件（注意替换为你实际安装的 PHP 版本号，例如 8.2）：
```
nano /etc/php/8.2/fpm/pool.d/www.conf
```
* **进程池动态调优**  
寻找以下参数并根据你的物理内存进行极其冷酷的计算（假设你的服务器是 2GB 内存，专供 Web 环境）：  
一个 PHP 进程大约消耗 30MB-50MB 内存。
```
pm = dynamic
# 最大子进程数。算力全开的极限值（2GB内存建议 50，4GB建议 100）
pm.max_children = 50
# 启动时默认开启的进程数
pm.start_servers = 10
# 最小空闲进程数（随时待命，应对突发流量）
pm.min_spare_servers = 10
# 最大空闲进程数（流量低谷时自动销毁，释放内存）
pm.max_spare_servers = 30
# 每个进程处理多少个请求后自动重启（防止 PHP 底层内存泄漏，极客必配！）
pm.max_requests = 500
```
保存后，重启 PHP 引擎：`systemctl restart php8.2-fpm`

**🐬 三、 MariaDB 缓冲池释放**  
数据库永远是 Web 架构中最致命的 I/O 瓶颈。机械硬盘或普通 SSD 的读写速度，在内存面前就像是蜗牛。

打开 MariaDB 的底层核心配置：
```
nano /etc/mysql/mariadb.conf.d/50-server.cnf
```

**InnoDB 黄金法则：**  
找到 `[mysqld]` 块，加入或修改 `innodb_buffer_pool_size`。  

它的作用是把数据库的索引和数据直接塞进内存里。如果你是一台专用的数据库服务器，这个值应该设置为物理内存的 50% - 70%。 但如果你的服务器同时还跑着 Nginx 和 PHP，请务必克制，设置为物理内存的 30% - 40% 即可。

```
[mysqld]
# 假设你是一台 2GB 内存的综合型服务器，分配 512M 给数据库引擎
innodb_buffer_pool_size = 512M

# 日志文件大小，通常设为 buffer_pool_size 的 25%
innodb_log_file_size = 128M
```
保存后，重启数据库引擎：`systemctl restart mariadb`

**🛡️ 四、 系统级内核参数（进阶网络）**  
你把 Nginx 的并发调到了 65535，但如果 Linux 操作系统的底层内核不允许，这一切都是徒劳！操作系统拥有最高的生杀大权。

> **突破文件描述符限制**  
Linux 哲学：一切皆文件（包括网络连接）。系统默认单进程只能打开 1024 个文件。
打开系统的限制文件：
```
nano /etc/security/limits.conf
```
在文件最末尾，输入系统级指令：  
```
* soft nofile 65535
* hard nofile 65535
root soft nofile 65535
root hard nofile 65535
```

> **TCP 连接复用与降维 (Sysctl)**
应对高并发和防止恶意 CC 攻击，我们需要让服务器极速回收 TCP 连接。
打开内核参数配置文件：
```
nano /etc/sysctl.conf
```
在文件末尾注入以下内核级网络调优代码：

```
# 开启 TCP 窗口缩放
net.ipv4.tcp_window_scaling = 1
# 开启重用机制，允许将 TIME-WAIT sockets 重新用于新的 TCP 连接
net.ipv4.tcp_tw_reuse = 1
# 缩短保持连接的时间 (默认为 7200 秒，降为 1200 秒)
net.ipv4.tcp_keepalive_time = 1200
# 提升内核 backlog 最大值
net.core.somaxconn = 65535
```

最后，执行终极指令让内核参数瞬间生效：
```
sysctl -p
```

至此，你的服务器已彻底解除封印，化身为一台性能狂暴的极客引擎！