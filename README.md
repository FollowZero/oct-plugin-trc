# octobercms trc插件

## 用到的tron功能

1.离线生成地址
2.trx转账
3.获取trx余额
4.trc20转账
5.获取trc20余额
6.离线签名
7.广播

## 实现的功能

1.会员生成地址
2.充值（会员给系统地址发送trc20代币，充值到系统币种）
3.归集 (将会员发送到系统地址上到trc20代币归集到主地址上)
4.提币 (给会员外网地址转账tr20代币)

## 用到的包【感谢】

+ [iexbase/tron-api](https://github.com/iexbase/tron-api)
+ [mattvb91/tron-trx-php](https://github.com/mattvb91/tron-trx-php)

## 其他
GenerateAddress
生成随机私钥和相应的账户地址. （存在安全风险，trongrid已经关闭此接口服务，请使用离线方式或者使用自己部署的节点）
刚用 iexbase/tron-api 跑通，个别接口就被关闭了。
又找到了 mattvb91/tron-trx-php 这个包，可以离线生成地址，本来只用这个包就可以实现所有功能，但是发现他好像不支持离线签名。懒的去中合，直接用这两个包了

