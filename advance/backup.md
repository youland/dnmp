# 💾 容灾备份与全量恢复 (Disaster Recovery)

> **🎯 极客战术**：不要相信任何云服务商的“99.99% 可靠性”承诺。硬件会老化，机房会断电，人会手滑“删库跑路”。在极客的法则中，**数据没有进行异地备份，就等于数据不存在；备份没有成功恢复过，就等于没有备份。**  
> **再垃圾的数据也有价值。**

这是你对抗物理毁灭和逻辑灾难的终极底牌。

---

### 📦 一、 核心资产盘点：你到底需要备份什么？

很多小白喜欢对整个 Linux 系统做“快照”或者“全盘镜像”，这不仅极其臃肿，而且在跨服务商迁移时几乎百分之百会遇到内核冲突。

真正的极客只做“精准切割”，对于 DNMP 架构，你的核心资产只有两块：
1. **物理文件资产**：`/var/www/` 目录下的所有网站源码、用户上传的图片附件。
2. **逻辑数据资产**：MariaDB 数据库中存储的文章、账号和业务数据。

只要这两样东西在手，无论换什么服务器，只需重新跑一遍 DNMP 脚本，你的网站就能在 10 分钟内满血复活。

---

### 🐬 二、 数据库的“冷热备份” (mysqldump)

数据库的数据是动态的，直接打包物理文件会导致数据损坏。我们必须使用原生的 `mysqldump` 工具进行逻辑备份。

**1. 极速全量导出 (带动态压缩)**  

单行导出指令。我们直接用管道符 `|` 将导出的 SQL 文本流接入 `gzip`，不仅不落地产生巨大的 `.sql` 文件，还能瞬间将体积压缩 80%：
```bash
mysqldump -u root -p你的数据库密码 你的数据库名 | gzip > /root/db_backup_$(date +%F).sql.gz
```
> ⚠️ 注意：-p 和密码之间不要有空格！例如密码是 123456，应该写成 -p123456

**2. 全量恢复**  

当服务器彻底崩溃并重装 DNMP 后，如何将数据导入新的数据库？  
先解压，再导入：
```
# 1. 解压备份文件
gzip -d /root/db_backup_2023-10-01.sql.gz

# 2. 将数据全量轰入空数据库
mysql -u root -p你的数据库密码 你的空数据库名 < /root/db_backup_2023-10-01.sql
```

**🗂️ 三、 网站源码的打包与精准过滤 (tar)**  

打包源码最忌讳把几 GB 的缓存文件和访问日志也打包进去，那是在浪费宝贵的磁盘 I/O。

**1. 极客级精准打包**  
使用 tar 命令，并利用 --exclude 参数无情地剔除掉那些随时可以重建的垃圾目录（比如 WordPress 的缓存）：
```
tar -czvf /root/web_backup_$(date +%F).tar.gz \
--exclude=/var/www/dnmp.com/wp-content/cache \
--exclude=/var/www/dnmp.com/wp-content/updraft \
/var/www/dnmp.com
```
> 代码解析：c 创建，z Gzip压缩，v 显示过程，f 指定文件名。


**2. 源码恢复**  
```
tar -xzvf /root/web_backup_2023-10-01.tar.gz -C /
```

 **🤖 四、 零信任异地拉取 (Zero-Trust Pull Backup)**

**🚨 极客生死线警告：拒绝“推”模型！**  
>很多教程会教你在 Web 服务器上写脚本，把备份文件“推（Push）”到远端 NAS 或备用机器上。这是极其致命的架构灾难！这意味着你的 Web 服务器上存有通往你 NAS 的钥匙。一旦 Web 服务器被黑，黑客就能拿着这把钥匙，把你的远端备份池一锅端！

**👑 极客破局法：上帝视角的“拉（Pull）”模型**  
>我们要把备份脚本写在绝对安全的机器上（比如你家里的 NAS、内网服务器），让它通过 SSH 像上帝一样，定时登录到 Web 服务器去“拉取”数据。Web 服务器本身不需要知道备份存在哪里，它就是个任人索取的无状态机器。

**1. 建立单向信任通道 (在 NAS 上操作)**  
在你的 NAS（或备用服务器）的终端里执行，将 NAS 的公钥塞进 Web 服务器，允许 NAS 免密登录 Web 服务器（注意替换端口和 IP）：
```bash
ssh-copy-id -p 8066 root@你的Web服务器IP
```
**2. 打造极客拉取脚本 (在 NAS 上编写)**  
在你的 NAS 服务器上创建备份脚本：
```
nano /root/pull_backup.sh
```
使用 `rsync` 进行极速增量拉取，并直接通过 `SSH` 管道将数据库流式拉回本地，Web 服务器上连临时文件都不会产生，彻底的数据不落地！  

以下只是示例，具体备份请按个人需求增减。
```
#!/bin/bash
# 运行在 NAS/备用机器 上的极客拉取脚本

# 目标 Web 服务器信息
WEB_IP="你的Web服务器IP"
WEB_PORT="8066"
DB_PASS="你的数据库密码"
DB_NAME="你的数据库名"
REMOTE_WEB_DIR="/var/www/dnmp.com/"

# 本地 NAS 存储路径
LOCAL_POOL="/volume1/backups/dnmp"
DATE=$(date +%Y%m%d)

mkdir -p $LOCAL_POOL

# 1. 流式拉取数据库 (管道传输，Web端不产生任何文件)
# 注意 -p 后面加了单引号
ssh -p $WEB_PORT root@$WEB_IP "mysqldump -u root -p'$DB_PASS' $DB_NAME | gzip" > $LOCAL_POOL/db_$DATE.sql.gz

# 2. 增量拉取网站源码 (rsync 极速比对，只下载变动过的文件)
rsync -avz --delete -e "ssh -p $WEB_PORT" \
--exclude="wp-content/cache" \
root@$WEB_IP:$REMOTE_WEB_DIR $LOCAL_POOL/web_latest/

# 3. 将拉取回来的最新源码打包封存
tar -czf $LOCAL_POOL/web_$DATE.tar.gz -C $LOCAL_POOL web_latest/

# 4. 自动销毁 NAS 上 30 天前的旧备份
find $LOCAL_POOL -maxdepth 1 -type f -mtime +30 -name "*.gz" -exec rm -f {} \;
```
赋予 NAS 上的脚本执行权限：
```
chmod +x /root/pull_backup.sh
```
**3. 注入 Cron 时间轴 (在 NAS 上执行)**  

让 NAS 的守护进程在每天凌晨 4:00 准时向 Web 服务器发起数据抽取  
`crontab -e`  
加入定时任务：  
`0 4 * * * /bin/bash /root/pull_backup.sh >/dev/null 2>&1`  

保存退出。
