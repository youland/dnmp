# ⚡ DNMP - 极简纯净 Web 服务器一键部署脚本
[![CC BY-NC-SA 4.0](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-lightgrey.svg)](http://creativecommons.org/licenses/by-nc-sa/4.0/)



**Debian + Nginx + MariaDB + PHP** 极简生产级环境

一行命令，快速部署高性能、安全、纯净的 Web 运行环境。拒绝臃肿面板，专注于极致性能与安全性。

---

## ✨ 核心特性

- **极致轻量**：纯命令行 + 模块化设计，无任何面板
- **智能源切换**：自动探测服务器位置，智能使用最优镜像源
- **安全优先**：严格权限控制，默认屏蔽未配置域名访问
- **性能优化**：默认开启 BBR、Opcache、Redis 支持
- **高度幂等**：支持重复执行，不会产生冗余配置
- **一键证书**：集成 acme.sh，支持自动签发 Let's Encrypt 证书
- **Docker集成**：选装 Docker 环境和 Portainer（可选 Docker 面板）



## 🚀 快速开始

### 一键安装（推荐）

```bash
curl -sSL https://dnmp.net/dnmp -O && bash dnmp ok
```

### 或手动下载安装  
```
wget https://dnmp.net/dnmp && chmod +x dnmp && ./dnmp ok
```

## 📋 运行环境  
* **支持系统**：Debian 12 (Bookworm) / Debian 13 (Trixie)
* **组件标准**：Nginx (最新稳定版)、MariaDB、PHP (动态适配)、Redis (可选)、acme.sh
* **部署建议**：推荐在最小化安装的纯净系统上运行```

> 推荐使用纯净系统（最小化安装）

## 📊 安装后默认包含

* Nginx（最新稳定版 + 深度优化配置）  
* MariaDB（最新稳定版）  
* PHP（动态适配当前最新版本）  
* Redis（可选安装）  
* phpMyAdmin  
* acme.sh（SSL 证书自动部署）  


## 🔧 常用命令
```
dnmp start          # 启动所有服务
dnmp stop           # 停止所有服务
dnmp restart        # 重启所有服务
dnmp reload         # 平滑重载配置
dnmp acme <域名>    # 签发 Let's Encrypt 证书
dnmp db             # 创建新数据库和用户
dnmp pw             # 修改 MariaDB root 密码
dnmp nlog           # 清理并归档 Nginx 日志
```

## ⚠️ 注意事项

* 请使用 root 权限运行脚本  
* 建议在纯净 Debian 系统上安装  
* 安装完成后请尽快修改 MariaDB root 密码  
* SSH 默认端口已修改为 8066  

## ❓ 常见问题
Q：安装完成后如何访问 phpMyAdmin？  
A：浏览器访问 http://你的IP/phpmyadmin

Q：如何签发 SSL 证书？  
A：执行 bash dnmp acme yourdomain.com  

Q：如何查看数据库密码？  
A：cat /root/.my.cnf  

## 📬 社区与支持

> GitHub 仓库：https://github.com/youland/dnmp  
问题反馈：欢迎提交 Issue

> 讨论区：https://bbs.dnmp.net

> 详细使用指南：https://dnmp.net





