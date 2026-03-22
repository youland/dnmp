# 🛡️ 终极防御与防线加固 (Security Hardening)

> **🎯 极客战术**：公网就是黑暗森林，所有的便捷都伴随着致命的风险。当我们将性能榨干到了极限之后，现在是时候关上那些不必要的门，为你的服务器建立一道叹息之墙了。

---

 **🚫 一、 免疫密码爆破：SSH 终极加固**

在安装web服务器环境中将 SSH 默认的 22 端口改成了 8066，这避开了 90% 的低级扫描器。但只要你还在使用“账号+密码”登录，暴力破解的威胁就永远存在。  
极客的终极破局法是：**彻底废弃密码，启用非对称加密密钥登录。**

**1. 锻造极客私钥 (在你的本地电脑上执行)**  

打开你本地的终端（Win10+ 可用 PowerShell，Mac/Linux 用 Terminal），生成目前最安全的 Ed25519 算法密钥：
```bash
ssh-keygen -t ed25519 -C "admin@dnmp"
```
一路回车即可。这会在你本地电脑的` ~/.ssh/` 目录下生成两个文件：`id_ed25519 私钥` 和 `id_ed25519.pub 公钥`。

**2. 将公钥注入服务器**  

将生成的公钥内容，复制到服务器的 ~/.ssh/authorized_keys 文件中。
```
cat id_ed25519.pub >> authorized_keys
```
或者直接在本地终端使用极其优雅的一键推送命令（注意替换你的 IP 和端口）：
```
ssh-copy-id -p 8066 root@你的服务器IP
```

**3. 🚨 斩断后路：彻底关闭密码验证**  

极客生死线警告：在执行这一步之前，务必先新开一个终端窗口，测试能否使用密钥成功免密登录！ 如果没成功就把密码关了，你将永远失去这台服务器的控制权！

确认密钥登录生效后，打开服务器的 SSH 核心配置文件：
```
nano /etc/ssh/sshd_config
```
找到以下配置，极其冷酷地将其改为 no：
```
PasswordAuthentication no
```
保存并重启 SSH 服务：`systemctl restart ssh`

**🧱 二、 铸造防火墙 (UFW)**  

Docker 经常会在底层悄悄修改 iptables，导致你的端口在公网上裸奔。我们需要用 UFW (Uncomplicated Firewall) 重新夺回网络出入口的绝对控制权。

**1. 开启默认拒绝策略（极度霸道）**  
默认拦截所有外部访问，只允许服务器内部主动向外发请求：
```
ufw default deny incoming
ufw default allow outgoing
```
**2. 精准放行生命线**  
只在叹息之墙上开三个极其微小的洞口：
```
ufw allow 8066/tcp  # 你的 SSH 管理端口
ufw allow 80/tcp    # Web HTTP
ufw allow 443/tcp   # Web HTTPS
```
**3. 激活叹息之墙**
```
ufw enable
```

**🥷 三、 Web 层的降维打击 (防扫描/防泄漏)**  

黑客在攻击前都会先“踩点”，我们要让服务器变成一个彻底的“黑匣子”，不给对方留下任何软件版本线索。

**1. 隐藏 Nginx 引擎版本号**  
```
nano /etc/nginx/nginx.conf
```
在 http { ... } 块中，找到并开启这行指令：
```
server_tokens off;
```
**2. 隐藏 PHP 版本号**  
```
nano /etc/php/8.2/fpm/php.ini
```
搜索 `expose_php`，将其无情关闭：
```
expose_php = Off
```
**3. 🚦 阻断 IP 直接访问 (极客必配)**  

很多恶意扫描器和未备案的黑产域名，会恶意解析到你的公网 IP 上。我们需要在 Nginx 中加一个“捕鼠夹”。
新建一个默认配置文件：
```
nano /etc/nginx/sites-enabled/default_drop.conf
```
写入极其冷酷的 444 阻断规则（444 是 Nginx 专属的非标准状态码，意为“直接切断连接，不返回任何内容”）：
```
server {
    listen 80 default_server;
    listen 443 ssl default_server;
    server_name _;
    
    # 随便挂载一个自签名证书或主域名的证书，用于应对 HTTPS 扫描
    ssl_certificate /etc/nginx/ssl/dnmp.com/dnmp.com.cer;
    ssl_certificate_key /etc/nginx/ssl/dnmp.com/dnmp.com.key;
    
    return 444;
}
```
保存并重启：`nginx -t && nginx -s reload`。现在，谁敢用 IP 直接访问你的服务器，他的连接就会被瞬间掐断！

**🐕 四、 恶犬看门：Fail2Ban (可选进阶)**  

即使你改了端口，依然会有一些闲得无聊的脚本在盲扫。Fail2Ban 就像一只凶猛的看门狗，它会盯着你的系统日志，一旦发现有人频繁尝试连接并失败，就直接在底层防火墙把他的 IP 封杀。

**1. 极速部署看门狗**
```
apt install fail2ban -y
```
**2. 配置看门狗规则**  

为了防止在系统升级时被覆盖，我们要克隆一份本地配置文件：
```
cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
nano /etc/fail2ban/jail.local
```
在文件中找到 `[sshd]` 块，将其激活，并修改为你真实的 SSH 端口：
```
[sshd]
enabled = true
port = 8066
filter = sshd
# 测谎仪标准：10分钟内失败 5 次，直接封禁该 IP 长达 24 小时
logpath = /var/log/auth.log
maxretry = 5
findtime = 600
bantime = 86400
```
启动恶犬：`systemctl restart fail2ban`

至此，你的服务器已经穿上了绝对防御的极客装甲。扫描器会崩溃，黑客会绝望，而你，可以在黑暗森林中安然入睡。