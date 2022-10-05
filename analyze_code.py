#!/usr/bin/env python3

from pprint import pprint
import subprocess
import sys

# cmd = ['grep', '-irZ', '--include=*.php', 'function', 'modules/ProvVoipEnvia']
cmd = ['grep', '-irZ', '--include=*.php', 'function', 'app', 'database', 'modules', 'tests', 'nmsprime_testsuite.php']

keywords = ('public', 'protected', 'private')

print(' '.join(cmd))
print()

called = subprocess.Popen(cmd,
        stdout=subprocess.PIPE,
        stderr=subprocess.STDOUT)

stdout, stderr = called.communicate()

result_raw = stdout.decode('utf-8').split('\n')


tmp = [r.split('\x00')[1].strip() for r in result_raw if r]
funcnames = [r.split('function')[1].strip().split('(')[0] for r in tmp if r.split(' ')[0] in keywords]

funcname_analysis = {
        'camel_case': [],
        'mixed_case': [],
        'snake_case': [],
        'single_word': [],
        }

for f in funcnames:
    tmp = f.lstrip('_')
    if any(c.isupper() for c in tmp) and '_' in tmp:
        funcname_analysis['mixed_case'].append(f)
    elif any(c.isupper() for c in tmp):
        funcname_analysis['camel_case'].append(f)
    elif '_' in tmp:
        funcname_analysis['snake_case'].append(f)
    else:
        funcname_analysis['single_word'].append(f)

funcname_analysis['camel_case'].sort()
funcname_analysis['mixed_case'].sort()
funcname_analysis['snake_case'].sort()
funcname_analysis['single_word'].sort()

pprint(funcname_analysis)
print()
print()
print('Function name analysis')
print('----------------------')
print('Total:       {:5}'.format(len(funcnames)))
print('Camel case:  {:5} ({:5.2f}%) (including overridden Laravel methods)'.format(
    len(funcname_analysis['camel_case']),
    round((100 * len(funcname_analysis['camel_case']) / len(funcnames)), 2)
))
print('Snake case:  {:5} ({:5.2f}%)'.format(
    len(funcname_analysis['snake_case']),
    round((100 * len(funcname_analysis['snake_case']) / len(funcnames)), 2)
))
print('Single word: {:5} ({:5.2f}%)'.format(
    len(funcname_analysis['single_word']),
    round((100 * len(funcname_analysis['single_word']) / len(funcnames)), 2)
))
print('Mixed case:  {:5} ({:5.2f}%) ({})'.format(
    len(funcname_analysis['mixed_case']),
    round((100 * len(funcname_analysis['mixed_case']) / len(funcnames)), 2),
    ', '.join(funcname_analysis['mixed_case'])
))
print()
