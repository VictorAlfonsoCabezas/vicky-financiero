const fs=require('fs'),path=require('path');
function walk(d){return fs.readdirSync(d,{withFileTypes:true}).flatMap(e=>e.isDirectory()?walk(path.join(d,e.name)):[path.join(d,e.name)]);}
const missingViews={}, missingComponents={};
for(const f of [...walk('app'),...walk('resources/views')].filter(f=>f.endsWith('.php'))){
 const s=fs.readFileSync(f,'utf8');
 for(const m of s.matchAll(/(?:\bview\(|@include\(|@extends\()\s*['"]([\w./-]+)['"]/g)) {
  const name=m[1],file='resources/views/'+name.replaceAll('.','/')+'.blade.php';
  if(!fs.existsSync(file)) (missingViews[name]??=[]).push(f);
 }
 for(const m of s.matchAll(/<livewire:([\w.-]+)/g)) {
  const cls=m[1].split('.').map(x=>x.split('-').map(y=>y[0].toUpperCase()+y.slice(1)).join('')).join('/');
  if(!fs.existsSync('app/Http/Livewire/'+cls+'.php')) (missingComponents[m[1]]??=[]).push(f);
 }
}
fs.writeFileSync('scripts/onix-view-check.json',JSON.stringify({missingViews,missingComponents},null,2));
console.log(JSON.stringify({missingViews,missingComponents},null,2));
