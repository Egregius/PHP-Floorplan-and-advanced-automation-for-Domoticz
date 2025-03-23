commandArray={}
local function isRealSensor(name)
	return otherdevices_idx[name]
end

for d, s in pairs(devicechanged) do
	if isRealSensor(d) then
		os.execute('wget -O /dev/null -o /dev/null "http://127.0.0.1/secure/pass2php.php?d='..d..'&s='..s..'" &')
	end
end
return commandArray
