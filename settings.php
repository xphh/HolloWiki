<?php
$svnuser = '15899';
$svnpwd = '`1q2w3e4r';

require_once("ac.php");
$ac = new AccessControl('SvnAuthorizor');
$ac->getAuthorizor()->setConfig('http://10.6.5.2/svnpl/ConsumeModule/Architecture/Desgin/%e8%ae%b8%e5%b9%b3/wiki.xml', $svnuser, $svnpwd);
$ac->auth();

require_once("source.php");

$source = SourceFactory::create('SvnSource', 'main', '主仓');
$source->setEncoding('GBK');
$source->setBasedir('http://10.6.5.2/svnpl/ConsumeModule/Architecture/Desgin/%e8%ae%b8%e5%b9%b3');
$source->setAuth($svnuser, $svnpwd);
$source = SourceFactory::proxy('DirBrowserProxy', $source);
$source = SourceFactory::proxy('CachedProxy', $source);

$source = SourceFactory::create('SvnSource', 'dp', 'DeviceProto');
$source->setEncoding('GBK');
$source->setBasedir('http://10.6.5.2/svnpl/BASPlatform/HSWX_APP/HSWXModule/DeviceProto/Trunk/md');
$source->setAuth($svnuser, $svnpwd);
$source = SourceFactory::proxy('DirBrowserProxy', $source);
$source = SourceFactory::proxy('CachedProxy', $source);

$source = SourceFactory::create('SvnSource', 'pj', 'ProtoJson');
$source->setEncoding('GBK');
$source->setBasedir('http://10.6.5.2/svnpl/BASPlatform/HSWX_APP/HSWXModule/HsviewClientSDK/Trunk/ProtoJson');
$source->setAuth($svnuser, $svnpwd);
$source = SourceFactory::proxy('ProtoJsonProxy', $source);
$source = SourceFactory::proxy('CachedProxy', $source);

$source = SourceFactory::create('SvnSource', 'hswx', '老华视眼');
$source->setEncoding('GBK');
$source->setBasedir('http://10.6.5.2/svn/Documents/SpecialProjects/BASPlatform/DH3.Z494_大华华视眼监控方案DH-IIS142/01产品文档/软件项目');
$source->setAuth('xu_ping', '123456');
$source = SourceFactory::proxy('DirBrowserProxy', $source);
$source = SourceFactory::proxy('CachedProxy', $source);

$source = SourceFactory::create('SvnSource', 'mts', 'MTS文档');
$source->setEncoding('GBK');
$source->setBasedir('http://10.6.5.2/svnpl/BASPlatform/HSWX_APP/HSWXModule/HSMTS/Docs');
$source->setAuth($svnuser, $svnpwd);
$source = SourceFactory::proxy('DirBrowserProxy', $source);
$source = SourceFactory::proxy('CachedProxy', $source);

$source = SourceFactory::create('SvnSource', 'share', '架构团队技术分享');
$source->setEncoding('GBK');
$source->setBasedir('http://10.6.5.2/svnpl/ConsumeModule/Architecture/Share');
$source->setAuth($svnuser, $svnpwd);
$source = SourceFactory::proxy('DirBrowserProxy', $source);
$source = SourceFactory::proxy('CachedProxy', $source);

/*
$source = SourceFactory::create('LofsSource', 'lo', '站点根目录');
$source->setEncoding('GBK');
$source->setBasedir('.');
*/

?>