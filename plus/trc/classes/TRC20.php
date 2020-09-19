<?php namespace Plus\Trc\Classes;

/**
 * Tcr20通用类
 */

use IEXBase\TronAPI\Provider\HttpProvider;
use IEXBase\TronAPI\Tron;
use Illuminate\Support\Facades\Crypt;
use kornrunner\Keccak;
use mattvb91\TronTrx\Address;
use mattvb91\TronTrx\Support\Base58;
use mattvb91\TronTrx\Support\Crypto;
use mattvb91\TronTrx\Support\Hash;
use Phactor\Key;

class TRC20
{
    protected $node;//节点
    //https://api.trongrid.io 正式节点
    //https://api.shasta.trongrid.io 测试节点
    protected $fullNode;
    protected $solidityNode;
    protected $eventServer;
    protected $tron;
    protected $new_address;//实例化的地址
    protected $new_key;//实例话的私钥
    protected $abi;//abi
    /**
     * 初始化
     */
    public function __construct($node,$new_address,$new_key)
    {
        $this->node=$node;
        $this->new_address=$new_address;
        $this->new_key=Crypt::decryptString($new_key);//统一解密私钥
        $this->fullNode = new HttpProvider($this->node);
        $this->solidityNode = new HttpProvider($this->node);
        $this->eventServer = new HttpProvider($this->node);
        //abi,可以用$this->tron->getContract($contract);获取，这里写死可少调一次接口
        $this->abi = json_decode('[{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"}],"name":"approve","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"INITIAL_SUPPLY","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_value","type":"uint256"}],"name":"safeWithdrawal","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_subtractedValue","type":"uint256"}],"name":"decreaseApproval","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"renounceOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transfer","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_addedValue","type":"uint256"}],"name":"increaseApproval","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"},{"name":"_spender","type":"address"}],"name":"allowance","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"name":"previousOwner","type":"address"}],"name":"OwnershipRenounced","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"previousOwner","type":"address"},{"indexed":true,"name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"owner","type":"address"},{"indexed":true,"name":"spender","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Transfer","type":"event"}]',true);
        try {
            $this->tron = new \IEXBase\TronAPI\Tron($this->fullNode, $this->solidityNode, $this->eventServer, null, null,$this->new_key);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
    }

    public function genKeyPair(): array
    {
        $key = new Key();

        return $key->GenerateKeypair();
    }

    public function getAddressHex(string $pubKeyBin): string
    {
        if (strlen($pubKeyBin) == 65) {
            $pubKeyBin = substr($pubKeyBin, 1);
        }

        $hash = Keccak::hash($pubKeyBin, 256);

        return Address::ADDRESS_PREFIX . substr($hash, 24);
    }

    public function getBase58CheckAddress(string $addressBin): string
    {
        $hash0 = Hash::SHA256($addressBin);
        $hash1 = Hash::SHA256($hash0);
        $checksum = substr($hash1, 0, 4);
        $checksum = $addressBin . $checksum;

        return Base58::encode(Crypto::bin2bc($checksum));
    }
    /**
     * 生成地址
     */
    public function createAccount(){
        //主节点不提供生成地址的api了。之前的这个作废
        //$res=$this->tron->createAccount();
        $res=$this->generateAddress();
        return $res;
    }

    /**
     * 本地生成地址
     * @return Address
     */
    public function generateAddress(): Address
    {
        $attempts = 0;
        $validAddress = false;

        do {
            if ($attempts++ === 5) {
                //这里应该是返回系统错误
                return false;
            }

            $keyPair = $this->genKeyPair();
            //带有0x的私钥。
            $privateKeyHex = $keyPair['private_key_hex'];
            $pubKeyHex = $keyPair['public_key'];

            //We cant use hex2bin unless the string length is even.
            if (strlen($pubKeyHex) % 2 !== 0) {
                continue;
            }

            $pubKeyBin = hex2bin($pubKeyHex);
            $addressHex = $this->getAddressHex($pubKeyBin);
            $addressBin = hex2bin($addressHex);
            $addressBase58 = $this->getBase58CheckAddress($addressBin);
            //不带0x的私钥
            $privateKey=substr($privateKeyHex,2);
            $address = new Address($addressBase58, $privateKey, $addressHex);
            $valid = $this->validateAddress($addressBase58);
            $validAddress=$valid['result'];
        } while (!$validAddress);

        return $address;
    }

    /**
     * 检查地址是否格式正确.
     * @param $address
     * @return array
     */
    public function validateAddress($address){
        $res=$this->tron->validateAddress($address);
        return $res;
    }

    /**
     * 激活地址
     * 在链上创建账号. 一个已经激活的账号创建一个新账号需要花费 0.1 TRX 或等值 Bandwidth.
     * @param string $address
     * @param string $newAccountAddress
     */
    public function registerAccount($address,$newAccountAddress){
        //创建接口数据
        $re=$this->tron->registerAccount($address,$newAccountAddress);
        //签名
        $signData=$this->tron->signTransaction($re);
        //广播
        $res=$this->tron->sendRawTransaction($signData);
        return $res;
    }

    /**
     * trx余额
     */
    public function getAccount($address){
        $res = $this->tron ->getBalance($address,true);
        return $res;
    }

    /**
     * trx转账
     * @param $add
     * @param $money
     */
    public function trxTransaction($add,$money)
    {
        $res = $this->tron->sendTransaction($add,$money,'',$this->new_address);
        return $res;
    }
    /**
     * 查询账户余额（代币）
     * @param $contract 合约地址
     * @param $address 账户地址
     * @param $dec 合约精度 6是usdt的精度
     * @return array
     */
    public function getAccountToToken($contract,$address,$dec=6)
    {
        $fun = "balanceOf";
        $toaddress = $this->tron->address2HexString($address);
        //这一步返回的是[object] (phpseclib\\Math\\BigInteger: 300000000)
        $balance_obj = $this->triggerConstantContract($contract,$fun,[$toaddress],$address);
        //trx格式的余额
        $balance_value=$balance_obj[0]->toString();
        //浮点型余额
        $address_balance = $this->fromTronExt($balance_value,$dec);
        return $address_balance;
    }
    /**
     * 代币转账
     * @param $contract
     * @param $toAddress
     * @param $accment
     */
    public function transferToToken($contract,$toAddress,$accment)
    {
        $func = "transfer";
        $toaddress = $this->tron->address2HexString($toAddress);
        $accment = $this->tron->toTron($accment);
        $params = [$toaddress,$accment];
        //1.创建交易
        $re = $this->triggerSmartContract($contract,$func,$params,$this->new_address);
        //2.签名
        $signData = $this->tron->signTransaction($re);
        //3.广播
        $res = $this->tron->sendRawTransaction($signData);
        return $res;
    }
    /**
     * 调用智能合约，返回 TransactionExtention, 需要签名后广播.
     */
    public function TriggerSmartContract($contract,$func,$params,$address){
        $res=$this->tron->getTransactionBuilder()->triggerSmartContract($this->abi,$this->tron->toHex($contract),$func,$params,100000000,$this->tron->toHex($address));
        return $res;
    }

    /**
     * 调用合约的常量函数, 需要函数类型为 view 或 pure.
     * 原包没有triggerConstantContract()github分支上有，自行复制，以下是地址
     * https://github.com/iexbase/tron-api/pull/53/commits/6669ced856e169bcad6d8853dd11f90a84547c4a
     */
    public function TriggerConstantContract($contract,$func,$params,$address){
        $res=$this->tron->getTransactionBuilder()->triggerConstantContract($this->abi,$this->tron->toHex($contract),$func,$params,$this->tron->toHex($address));
        return $res;
    }

    /**
     * 测试时写的此方法，正式用直接调$this->tron->getcontract
     * 查询区块链中的智能合约信息, 包括合约二进制代码, ABI, 配置参数等.
     * @param $contract 合约地址
     * @return array
     */
    public function getContract($contract){
        $res=$this->tron->getContract($contract);
        return $res;
    }

    /**
     * Convert trx to float
     * 可传精度的fromTron
     * @param $amount
     * @return float
     */
    public function fromTronExt($amount,$dec=6): float{
        $dec_str=pow(10,$dec);
        return (float) bcdiv((string)$amount, (string)$dec_str, 8);
    }

    /**
     * Convert float to trx format
     *可传精度的toTron
     * @param $double
     * @return int
     */
    public function toTronExt($double,$dec=6): int {
        $dec_str=pow(10,$dec);
        return (int) bcmul((string)$double, (string)$dec_str,0);
    }

}
