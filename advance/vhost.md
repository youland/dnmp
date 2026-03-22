# 🗂️ 多站点部署与并发引流 (Vhost)

> 🎯 极客战术：一台满血的服务器只跑一个网站，是对算力的极大浪费。真正的极客懂得利用 Nginx 的“影分身之术”，在同一套底层引擎上，优雅且互不干扰地并发驱动十几个甚至几十个独立的 Web 站点。

当你掌握了虚拟主机（Virtual Host）的配置逻辑，你的服务器将彻底进化为一个小型的“极客数据中心”。

**📂 一、 物理切割：绝对的资产隔离**  
多站点部署的第一个大忌，就是把所有网站的源码混在同一个目录里。这不仅会让后续的容灾备份变成噩梦，一旦某个站点被黑客植入木马，还会引发可怕的“跨站感染”。

* **建立标准化的目录矩阵**  
我们强制要求以域名作为文件夹命名标准。假设你要部署一个新站点 app.dnmp.com，请先为其开辟专属领地目录：
``` 
mkdir -p /var/www/app.dnmp.com 
```
现在可以把网站的程序上传到这个目录之中了。

* **⚠️ 极其重要的权限护城河**  
目录建好后，它默认属于 `root` 用户。但 `Nginx` 和 `PHP-FPM` 在底层是以 `www-data` 这个低权限影子用户运行的。如果不移交权限，你的网站将无法上传任何图片，甚至连缓存都写不进去！
必须执行这行降维指令，赋予 `Web` 引擎读写权限：
```
chown -R www-data:www-data /var/www/app.dnmp.com
```

**🔒 二、 绿锁先行：HTTPS 自动化接管**  
在传统的运维流程中，你需要手动去复制 Nginx 配置文件，再去单独申请 SSL 证书。
但在咱们这套 DNMP 脚本中，请直接祭出之前的“自动化武器”。

无论你要加第几个网站，只要把新域名的 DNS 解析到这台服务器，然后直接执行：
```
bash dnmp acme app.dnmp.com
```

**☕ 极客提示：** 这行指令犹如一把瑞士军刀，它不仅会自动为你签发 Let's Encrypt 证书，还会自动在 `/etc/nginx/sites-enabled/` 目录下为你生成一份以该域名命名的专属 Nginx 配置文件！

**⚙️ 三、 核心脑域：配置文件的微调与装载**  
ACME 脚本为你生成了基础的 HTTPS 配置文件，接下来，我们需要打开它，告诉 Nginx 这个新站点的具体位置和记录方式。

用编辑器打开刚才生成的配置：
```bash
nano /etc/nginx/sites-enabled/app.dnmp.com.conf
```
在极其复杂的 Nginx 语法中，你只需要死死盯住并修改三个核心锚点：

* `server_name` **(流量嗅探器)**  
确保它写的是你的新域名，Nginx 就是靠这个字段来区分并发流量的。
```
server_name app.dnmp.com;
```

* `root` **(物理映射路径)**  
将它指向我们第一步创建的专属隔离目录。
```
root /var/www/app.dnmp.com;
```

* `access_log` & `error_log` (**独立黑匣子)**  
绝不要把所有网站的日志混在一起！必须为新站点指定独立的日志文件，这是未来排错和防 CC 攻击的唯一凭证：
```
access_log /var/log/nginx/app.dnmp.com.access.log;
error_log /var/log/nginx/app.dnmp.com.error.log;
```

保存退出后，永远记得用极客的优雅方式重载引擎（先测试语法，再平滑重启，绝不中断现有站点的访客）：
```
nginx -t && nginx -s reload
```

**🚦 四、 高阶战术：SEO 流量重定向 (301)**  

很多站长会同时解析 `www.dnmp.com` 和 `dnmp.com`。为了收拢搜索引擎权重，我们需要做 301 强制跳转。

* **🚨 极客避坑指南：TLS 握手悖论**  
记住，即使是做跳转，`Nginx` 也必须先和浏览器完成合法的 `SSL` 握手。因此，**你必须也为 www 域名单独申请一张证书！**

1. 先执行签发命令：`bash dnmp acme www.dnmp.com`
2. 再执行命令：`bash dnmp acme dnmp.com`，签发另个一外SSL证书。

* **极客收纳术：合并配置文件**  
为了保持配置目录的绝对整洁，我们不建议保留多个零散的文件。我们将采用**“双 Server 合璧”**的写法，把跳板规则直接写进主站的配置中。

打开你主站的 Nginx 配置文件，进行编辑
```
nano /etc/nginx/sites-enabled/dnmp.com.conf
```
删除里面所有信息，写入以下内容：  
```
# 流量跳转，把www.dnmp.com的流量全部跳转到dnmp.com
server {
    listen 80;
    listen 443 ssl;
    server_name www.dnmp.com;

    # 挂载为 www 单独申请的专属证书！
    ssl_certificate /etc/nginx/ssl/www.dnmp.com/www.dnmp.com.cer;
    ssl_certificate_key /etc/nginx/ssl/www.dnmp.com/www.dnmp.com.key;

    #  301 永久重定向，将参数原封不动带回主域名
    return 301 https://dnmp.com$request_uri;
}

#  主站配置（保持原样，无需大改）
server {
    listen 80;
    listen 443 ssl;
    server_name dnmp.com;
    
    ssl_certificate /etc/nginx/ssl/dnmp.com/dnmp.com.cer;
    ssl_certificate_key /etc/nginx/ssl/dnmp.com/dnmp.com.key;

    root /var/www/dnmp.com;
    # ... 下面是其他的伪静态、PHP 解析等代码 ...
}
```
再次 `nginx -s reload`。至此，你的服务器已经具备了完美的多站点并发处理能力，且所有的流量规则都在你的绝对掌控之中！

**🚀 五、 终端交付与业务访问**

当 Nginx 成功平滑重载的那一刻，你的全新站点已经物理连通。现在，请打开浏览器，在地址栏敲下你刚刚配置好的域名，见证绿锁亮起。

* **静态部署验收**：如果你只是放了一个简单的 `index.html`，此时页面应该已经能够被公网完美访问。
* **动态 CMS 唤醒安装**：如果你通过 SFTP 上传了诸如 WordPress、Typecho 等开源 CMS 的源码，此时 Nginx 会将请求精准交给 PHP-FPM，页面将自动唤醒该系统的图形化安装向导 (Install Wizard)。请配合你之前在数据库中创建的专属账号密码，完成最后的数据对接。

> **⚠️ 极客排错预警**：  
> 在执行 CMS 在线安装时，如果页面报出 **“无法创建配置文件”** 或 **“目录不可写”** 的致命错误，这百分之百是因为你漏掉了第一步的权限移交。请立即切回 SSH 终端，重新对该站点目录执行降维打击：`chown -R www-data:www-data /var/www/你的站点目录`。