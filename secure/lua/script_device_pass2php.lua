for d,s in pairs(devicechanged)
do
os.execute('curl -X POST -d "d='..d.."&s="..s..'" http://127.0.0.1/secure/pass2php.php &')
end
commandArray={}