import sys, json
data = json.loads(sys.argv[1])

data['gold'] *= 10000
data['silver'] *= 100
summ = sum(data.values())
print(summ)