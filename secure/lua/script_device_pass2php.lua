for d,s in pairs(devicechanged)
do
os.execute('wget -O /dev/null -o /dev/null "http://127.0.0.1/secure/pass2php.php?d='..d..'&s='..s..'" &')
end
commandArray={}