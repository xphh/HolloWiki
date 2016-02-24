# HolloWiki

总有一些傻帽公司喜欢在SVN上做文档存储和知识管理，然后又舍不得设置为public权限。HolloWiki就是为这种场景服务的：你可以把Markdown文档以及其他任意文档存储在SVN上，作为版本管理和权限控制；同时也可以通过Wiki系统展现出来，分享出去。

## 配置

前提：
1. 当前HolloWiki运行在PHP 5.2及以上版本。
2. 需要安装subversion命令行工具。

自定义：
* 在settings.php中设置SVN地址（必须是http或https地址）以及用户名和密码。
* 可以更改css/markdown.css以获取更佳的Markdown渲染效果。
* 可以自行修改template.html以获取自定义的页面布局。

## 使用

HolloWiki页面左侧是目录浏览器，你可以点击进入目录，也可以下载各种文件。

如果点击的是以md为后缀的文件，HolloWiki会进行解析，并展示在页面右侧。另外，当进入某个目录，HolloWiki会搜寻名为`Home.md`的文件，若存在则自动展示。

以md为后缀的文件遵循Markdown（GFM）语法。但是为了在HolloWiki中使用方便，扩展了2种链接方式：

* 以`[text](#link)`形式的链接，会链接到`当前目录/link`的资源上。
* 以`[[text]]`形式的链接，会链接到`当前目录/text.md`的资源上。

