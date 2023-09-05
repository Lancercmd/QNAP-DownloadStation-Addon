# QNAP Download Station Addon
Unofficial QNAP Download Station Addon repository for additional torrent providers.
## 蜜柑计划 - Mikan Project
Download Station ≥ 5.0.0

Download Station 5 的 WebUI 侧，其 BT 搜索看起来是不应用代理设置的，所以如果没有路由器科学上网应该是刷不出东西的。

所以我自己在 WebUI 上也用不了，哎哎哎。
| 功能 | 实现 |
| - | :-: |
| RSS 摘要解析 | ✅ |
| 搜索 | ✅ |
| 网址解析| - |
## 直接安装
因为没法正确地生成密钥对，所以打包不了，幸好手动安装也不麻烦。
## 手动安装
1. 你有一个已安装了 Download Station 5 的 QNAP NAS。
1. 将 mikanani.me 文件夹拷贝到 NAS 上的位置比如 Public 文件夹。
1. 通过 SSH 连接到 QNAP NAS。
1. 将 mikanani.me 文件夹拷贝到插件目录。
```bash
# 示例为 QTS 5.1.1.2491
cp -r /share/Public/mikanani.me /share/CACHEDEV1_DATA/.qpkg/DownloadStation/usr/sbin/addons/mikanani.me
```
最后在 Download Station 5 - 设置 - 附加组件 点击更新，新添加的组件就会出现，可以通过编辑对应项来启用或停用组件。